<?php

namespace Smuuf\Primi;

use Smuuf\Primi\Ex\ControlFlowException;
use Smuuf\Primi\Ex\RuntimeError;

/**
 * Direct abstract syntax tree interpreter.
 * @see https://en.wikipedia.org/wiki/Interpreter_(computing)#Abstract_Syntax_Tree_interpreters
 */
class Interpreter extends \Smuuf\Primi\StrictObject {

	/**
	 * Path to temporary directory where ASTs will be cached.
	 * If `null`, AST cache will not be used at all.
	 *
	 * @var null|string
	 */
	private $tempDir;

	/** @var IContext */
	private $context;

	public function __construct(
		?IContext $context = null,
		?string $tempDir = null,
		?ExtensionHub $extHub = null
	) {

		$this->tempDir = $tempDir !== null
		? rtrim($tempDir, "/")
			: null;

		$context = $context ?: new Context;
		$extHub = $extHub ?? new ExtensionHub;
		$extHub->applyToContext($context);

		$this->context = $context;
		$this->context->setInterpreter($this);

	}

	public function getContext(): IContext {
		return $this->context;
	}

	public function run(string $source) {

		$ast = $this->getSyntaxTree($source);

		// Each node must have two keys: 'name' and 'text'.
		// These are provided by the PHP-PEG itself, so we should be able to
		// be counting on it.

		// We begin the process of interpreting a source code simply by
		// passing the AST's root node to its dedicated handler (determined by
		// node's "name").

		try {

			$handler = HandlerFactory::get($ast['name']);
			return $handler::handle($ast, $this->context);

		} catch (ControlFlowException $e) {
			$what = $e::ID;
			throw new RuntimeError("Cannot '{$what}' from global scope");
		}

	}

	public function getSyntaxTree(string $source): array {

		if (
			$this->tempDir !== null
			&& ($ast = $this->loadCachedAST($source))) {
			return $ast;
		}

		$parser = new ParserHandler($source);
		$ast = $parser->run();

		if ($this->tempDir !== null) {
			$this->storeCachedAST($ast, $source);
		}

		return $ast;

	}

	protected function loadCachedAST(string $source) {

		$path = self::buildCachedPath($source, $this->tempDir);
		if (is_file($path)) {
			return json_decode(file_get_contents($path), true);
		}

		return false;

	}

	protected function storeCachedAST(array $ast, string $source) {
		$path = self::buildCachedPath($source, $this->tempDir);
		file_put_contents($path, json_encode($ast));
	}

	private static function buildCachedPath(string $source, string $path): string {
		return $path . sprintf('/ast_cache_%s.json', md5($source));
	}

}
