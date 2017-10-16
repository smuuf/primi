<?php

namespace Smuuf\Primi;

/**
 * Direct abstract syntax tree interpreter.
 * @see https://en.wikipedia.org/wiki/Interpreter_(computing)#Abstract_Syntax_Tree_interpreters
 */
class Interpreter extends \Smuuf\Primi\StrictObject {

	private $tempDir;
	private $context;

	public function __construct(Context $context = null, string $tempDir = null) {

		$this->tempDir = $tempDir ?: false;
		$this->context = $context ?: new Context;

	}

	public function getContext() {
		return $this->context;
	}

	public function run(string $source) {

		$ast = $this->getSyntaxTree($source);

		// Each node must have two keys: 'name' and 'text'.
		// These are provided by the PHP-PEG itself, so we should be able to count on it.

		$handler = HandlerFactory::get($ast['name']);
		$handler::handle($ast, $this->context);

	}

	public function getSyntaxTree(string $source):array {

		if ($this->tempDir && $ast = $this->loadCachedAST($source)) {
			return $ast;
		}

		$parser = new ParserHandler($source);
		$ast = $parser->run();

		if (!$this->tempDir) {
			return $ast;
		}

		$this->storeCachedAST($ast, $source);
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

	private static function buildCachedPath(string $source, string $path) {
		return $path . sprintf('/ast_cache_%s.json', md5($source));
	}

}
