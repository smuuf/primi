<?php

declare(strict_types=1);

namespace Smuuf\Primi\Code;

use Smuuf\StrictObject;
use Smuuf\Primi\Ex\SyntaxError;
use Smuuf\Primi\Ex\InternalSyntaxError;
use Smuuf\Primi\Parser\ParserHandler;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\KnownHandlers;
use Smuuf\Primi\Code\Serialization\Serializer;

class BytecodeProvider {

	use StrictObject;

	private \Closure $bytecodeGetter;

	public function __construct(?string $tempDir = \null) {
		$this->bytecodeGetter = $this->buildBytecodeGetter($tempDir);
	}

	/**
	 * @return array
	 * @phpstan-return TypeDef_AstNode
	 */
	public static function parse(Source $source): array {
		return (new ParserHandler($source->getSourceString()))->run();
	}

	/**
	 * @param mixed $args
	 */
	public static function compile(Source $source, ...$args): Bytecode {

		try {
			$ast = self::parse($source);
			return (new Compiler($ast))->compile(...$args)->toFinalBytecode();
		} catch (InternalSyntaxError $e) {
			throw SyntaxError::fromInternal($e, $source);
		}

	}

	public function getBytecode(Source $source): Bytecode {
		return ($this->bytecodeGetter)($source);
	}

	private function buildBytecodeGetter(
		?string $tempDir = \null,
	): \Closure {

		// NOTE: $tempDir is taken from Config object - that means we're sure
		// it already is an existing directory - or null.

		$getter = static fn(Source $source): ?Bytecode => self::compile($source);

		if ($tempDir) {
			$getter = function(Source $source) use ($getter, $tempDir): ?Bytecode {

				$key = \json_encode([
					KnownHandlers::getStateId(),
					$source->getSourceString(),
				]);

				if ($bytecode = $this->loadFromCache($key, $tempDir)) {
					return $bytecode;
				}

				$bytecode = $getter($source);
				$this->storeIntoCache($key, $tempDir, $bytecode);

				return $bytecode;

			};
		}

		return $getter;

	}

	private function loadFromCache(string $key, string $dir): ?Bytecode {

		$path = self::buildCachedPath($key, $dir);
		if (\is_file($path)) {
			return Serializer::unserialize(file_get_contents($path));
		}

		return \null;

	}

	private function storeIntoCache(
		string $key,
		string $dir,
		Bytecode $code,
	): void {

		$path = self::buildCachedPath($key, $dir);
		$data = Serializer::serialize($code);
		file_put_contents($path, $data);

	}

	private static function buildCachedPath(string $key, string $dir): string {
		return \sprintf('%s/ast_cache_%s.primibc', $dir, \md5($key));
	}

}
