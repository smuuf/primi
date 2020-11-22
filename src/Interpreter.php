<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Structures\Value;

/**
 * Primi's primary abstract syntax tree interpreter.
 */
class Interpreter extends RawInterpreter {

	/**
	 * Path to temporary directory where ASTs will be cached.
	 * If `null`, AST cache will not be used at all.
	 *
	 * @var null|string
	 */
	private $tempDir;

	/**
	 * Runtime context the interpreter works with.
	 * @var Context
	 */
	private $context;

	/**
	 * Create a new instance of interpreter.
	 *
	 * @param string|null $tempDir _(optional)_ Path to temporary directory used
	 * to cache parsed AST. If not specified, parsed Primi files will not be
	 * cached.
	 * @param ExtensionHub|null $extHub _(optional)_ Instance of extension hub
	 * to be used.
	 */
	public function __construct(
		$scopeOrContext = null,
		?string $tempDir = null,
		?ExtensionHub $extHub = null
	) {

		// If temp directory was not specified, do not cache parsed AST.
		$tempDirPath = $tempDir !== null
			? realpath($tempDir) // Returns false if path does not exist.
			: null;

		if ($tempDirPath === false) {
			throw new EngineError("Directory '$tempDirPath' does not exist");
		}

		// Interpreter is created with a global scope provided or with nothing
		// at all.
		if ($scopeOrContext === null || $scopeOrContext instanceof AbstractScope) {

			$globalScope = $scopeOrContext ?? new Scope;
			$extHub = $extHub ?? new ExtensionHub;
			$extHub->apply($globalScope);

			$ctx = new Context;
			$ctx->pushScope($globalScope);

		} elseif ($scopeOrContext instanceof Context) {
			$ctx = $scopeOrContext;
		} else {
			throw new EngineError("First argument passed to Interpreter must be AbstractScope|Context|null");
		}

		$this->context = $ctx;

	}

	/**
	 * Returns scope the runtime is currently in.
	 *
	 * Most probably this will be the global scope, unless there are some
	 * shenanigans going on with concurrency and this method is called when the
	 * interpreter is currently in some other, nested scope.
	 */
	public function getCurrentScope(): AbstractScope {
		return $this->context->getCurrentScope();
	}

	/**
	 * Returns the primary runtime context.
	 */
	public function getContext(): Context {
		return $this->context;
	}

	/**
	 * Main entrypoint for running a Primi source code provided as text.
	 */
	public function run(string $source): Value {

		$this->context->pushCall('<program>');
		return $this->execute($source, $this->context);

	}

	public function getSyntaxTree(string $source): array {

		// Use cached parsed AST, if caching is enabled and cached AST is
		// available for this source code string.
		if ($ast = $this->loadAST($source)) {
			return $ast;
		}

		$ast = parent::getSyntaxTree($source);

		// Store/cache parsed AST, if caching is enabled.
		$this->storeAST($ast, $source);

		return $ast;

	}

	protected function loadAST(string $source): ?array {

		if ($this->tempDir === null) {
			return null;
		}

		$path = self::buildCachedPath($source, $this->tempDir);
		if (is_file($path)) {
			return json_decode(file_get_contents($path), true);
		}

		return null;

	}

	protected function storeAST(array $ast, string $source): void {

		if ($this->tempDir === null) {
			return;
		}

		$path = self::buildCachedPath($source, $this->tempDir);
		file_put_contents($path, json_encode($ast));

	}

	private static function buildCachedPath(string $source, string $path): string {
		return $path . sprintf('/ast_cache_%s.json', md5($source));
	}

}
