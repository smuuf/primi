<?php

declare(strict_types=1);

namespace Smuuf\Primi\VM;

use Smuuf\StrictObject;
use Smuuf\Primi\Scope;
use Smuuf\Primi\VM\TryBlock;
use Smuuf\Primi\Code\Bytecode;
use Smuuf\Primi\Structures\ThrownException;
use Smuuf\Primi\Values\ModuleValue;
use Smuuf\Primi\Values\ExceptionValue;

class Frame {

	use StrictObject;

	/**
	 * Tracking the index of the current opcode.
	 * This is updated when VM calls Primi functions - which can then be used
	 * for building tracebacks (where we need to know which opcode did the
	 * call).
	 */
	private int $opIndex = 0;

	/** @var \SplStack<TryBlock> */
	private \SplStack $tryStack;

	private ValueStack $valueStack;
	public readonly int $callStackSize;

	public function __construct(
		private $name,
		private Scope $scope,
		private ModuleValue $module,
		private ?Bytecode $bytecode,
		private ?self $parent,
	) {

		$this->valueStack = new ValueStack();
		$this->tryStack = new \SplStack();

		$this->callStackSize = $parent
			? $parent->callStackSize + 1
			: 0;

	}

	/**
	 * This is necessary for import mechanism, where frame object is already
	 * created, but we'll get the bytecode sometimes later.
	 */
	public function withBytecode(Bytecode $bytecode): self {

		$clone = clone $this;
		$clone->bytecode = $bytecode;
		return $clone;

	}

	public function getName(): string {
		return $this->name;
	}

	public function getBytecode(): ?Bytecode {
		return $this->bytecode;
	}

	public function getScope(): ?Scope {
		return $this->scope;
	}

	public function getModule(): ModuleValue {
		return $this->module;
	}

	public function getValueStack(): ValueStack {
		return $this->valueStack;
	}

	public function getParent(): ?self {
		return $this->parent;
	}

	/**
	 * Push a new TryBlock onto the try-stack.
	 * This signifies entering a try-block.
	 */
	public function pushTry(TryBlock $tryBlock): void {
		$this->tryStack->push($tryBlock);
	}

	/**
	 * Pop the top TryBlock off the try-stack.
	 * This signifies exiting a try-block.
	 */
	public function popTry(): void {
		$this->tryStack->pop();
	}

	/**
	 * Tries to find a try-catch block present in current frame that will catch
	 * the specified exception.
	 *
	 * Returns the catch handler's opcode index within frames bytecode if found.
	 * Returns null otherwise.
	 *
	 * @return null|array{int, int, ?string}
	 */
	public function findCatch(ThrownException $thrownExc): ?array {

		$innerExc = $thrownExc->exception;

		/** @var TryBlock $tryBlock */
		foreach ($this->tryStack as $tryBlock) {
			[$index, $varName] = $tryBlock->resolveJumpIndex($innerExc);
			if ($index !== null) {
				return [$index, $tryBlock->originalStackSize, $varName];
			}
		}

		return null;

	}

	public function storeOpIndex(int $i): void {
		$this->opIndex = $i;
	}

	public function getOpIndex(): int {
		return $this->opIndex;
	}

}
