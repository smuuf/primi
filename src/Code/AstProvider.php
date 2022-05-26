<?php

declare(strict_types=1);

namespace Smuuf\Primi\Code;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Location;
use \Smuuf\Primi\Ex\SyntaxError;
use \Smuuf\Primi\Ex\InternalSyntaxError;
use \Smuuf\Primi\Parser\ParserHandler;

class AstProvider {

	use StrictObject;

	/** Path to temporary directory for storing cached AST files. */
	private ?string $tempDir;

	public function __construct(?string $tempDir = \null) {

		// $tempDir is taken from Config object - that means we're sure
		// it already is an existing directory - or null.
		$this->tempDir = $tempDir;

	}

	public function getAst(Source $source, bool $caching = \true): Ast {

		if (!$caching) {
			return new Ast(self::parseSource($source));
		}

		$key = \md5($source->getSourceCode());
		if ($ast = $this->loadFromCache($key)) {
			return new Ast($ast);
		}

		$ast = self::parseSource($source);

		// Store/cache parsed AST, if caching is enabled.
		$this->storeIntoCache($key, $ast);

		return new Ast($ast);

	}

	/**
	 * @return TypeDef_AstNode|null
	 */
	private function loadFromCache(string $key): ?array {

		if ($this->tempDir === \null) {
			return \null;
		}

		$path = self::buildCachedPath($key, $this->tempDir);
		if (\is_file($path)) {
			return json_decode(file_get_contents($path), \true);
		}

		return \null;

	}

	/**
	 * @param TypeDef_AstNode $ast
	 */
	private function storeIntoCache(string $key, array $ast): void {

		if ($this->tempDir === \null) {
			return;
		}

		$path = self::buildCachedPath($key, $this->tempDir);
		file_put_contents($path, json_encode($ast));

	}

	/**
	 * @return TypeDef_AstNode
	 * @throws SyntaxError
	 */
	private static function parseSource(Source $source): array {

		$sourceString = $source->getSourceCode();

		try {
			return (new ParserHandler($sourceString))->run();
		} catch (InternalSyntaxError $e) {
			throw SyntaxError::fromInternal($e, $source);
		}

	}

	private static function buildCachedPath(string $key, string $dir): string {
		return \sprintf('%s/ast_cache_%s.json', $dir, $key);
	}

}
