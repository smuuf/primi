<?php

declare(strict_types=1);

namespace Smuuf\Primi\Compiler;

use Closure;
use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Ex\EngineInternalError;
use Smuuf\Primi\Code\OpLocation;
use Smuuf\Primi\Compiler\BytecodeDLL;
use Smuuf\Primi\Handlers\HandlerFactory;

class Compiler {

	use \Smuuf\StrictObject;

	public BytecodeDLL $bytecode;

	private const META_DEFAULT_SENTINEL = "\x07__meta__sentinel";

	/** @var array<string|int, true> */
	private array $labels = [];

	/** @var \SplStack<array<mixed>> */
	private \SplStack $metaFrameStack;

	/** @var array{?int, ?int} */
	private array $currentLocation = [null, null];

	/**
	 * @param array $rootNode
	 * @phpstan-phpstan-param TypeDef_AstNode $rootNode
	 */
	public function __construct(
		private array $rootNode,
		private CodeType $codeType = CodeType::CodeGlobal,
	) {
		$this->metaFrameStack = new \SplStack();
	}

	/**
	 * @param bool $keepValue If `true` the latest OP_POP is removed from
	 *     the bytecode so the latest value on value stack will be kept as
	 *     return value. (This is primarily used by REPL - we want to return
	 *     the result of latest expression.)
	 */
	public function compile(
		bool $keepValue = false,
	): BytecodeDLL {

		$this->bytecode = new BytecodeDLL;
		$this->withMetaFrame(fn() => $this->inject($this->rootNode));

		// If $keepValue is true, find the latest OP_POP and remove it (using
		// splicing with empty replacement).
		if ($keepValue && $this->bytecode->count()) {

			/** @var Op */
			$lastOp = $this->bytecode->top();
			if ($lastOp->opType === Machine::OP_POP) {
				$this->bytecode->pop();
			}

		}

		// Add the final return, which pops the top value off value stack and
		// that will be the return value of the bytecode.
		$this->add(Machine::OP_RETURN);

		PostProcessor::doPostProcessing($this->bytecode);

		return $this->bytecode;

	}

	/**
	 * @param array $node
	 * @phpstan-phpstan-param TypeDef_AstNode $node
	 * @return class-string
	 */
	public function inject(array $node): string {

		// Each injection acts upon a AST "node", which contains information
		// about location where the "node" sits within a source code string.
		$this->currentLocation = [$node['_l'], $node['_p']];

		$handler = HandlerFactory::getFor($node['name']);
		$handler::compile($this, $node);

		return $handler;

	}

	/**
	 * @param string $opType
	 * @param mixed $args
	 * @phpstan-param Machine::OP_* $opType
	 */
	public function add(string $opType, ...$args): void {

		$location = new OpLocation(
			$this->currentLocation[0],
			$this->currentLocation[1],
		);

		$op = new Op($opType, $args, $location);
		$this->bytecode->push($op);

	}

	public function pop(): ?Op {

		try {

			$popped = $this->bytecode->pop();

			// Reconstruct the "current position" from the new top Op on the
			// Ops stack, if there's any.
			if ($this->bytecode->count()) {
				/** @var ?Op */
				$top = $this->bytecode->top();
				$this->currentLocation = [$top?->opLoc->line, $top?->opLoc->pos];
			}

			return $popped;

		} catch (\RuntimeException) {
			return null;
		}

	}

	public function insertLabel(Label $label): void {

		$this->add(Machine::OP_LABEL, $label);

		// Remember the just added label ID, so we can track uniqueness of
		// labels.
		$this->labels[$label->id] = true;

	}

	/**
	 * Generates a string label ID that's guaranteed to be unique within
	 * current bytecode.
	 */
	public function createLabel(): Label {

		do {
			$id = substr(md5(random_bytes(16)), 0, 16);
		} while (isset($this->labels[$id]));

		return new Label($id);

	}

	/**
	 * Wrap executing the provided closure with a new meta-frame.
	 */
	public function withMetaFrame(\Closure $body): void {

		$this->metaFrameStack->push(new \ArrayObject());
		$body();
		$this->metaFrameStack->pop();

	}

	public function setMeta(
		MetaFlag $key,
		mixed $value
	): void {
		$this->metaFrameStack->top()[$key->name] = $value;
	}

	public function getMeta(
		MetaFlag $key,
		mixed $default = self::META_DEFAULT_SENTINEL,
	): mixed {

		$currentMetaFrame = $this->metaFrameStack->top();
	 	if ($currentMetaFrame->offsetExists($key->name)) {
			return $currentMetaFrame[$key->name];
		}

		if ($default === self::META_DEFAULT_SENTINEL) {
			throw new EngineInternalError("Current meta is missing key '{$key->name}'");
		}

		return $default;

	}

	public function getCodeType(): CodeType {
		return $this->codeType;
	}

}
