<?php

declare(strict_types=1);

namespace Smuuf\Primi\Parser;

use \Smuuf\StrictObject;
use \Smuuf\Primi\EnvInfo;

use \hafriedlander\Peg\Compiler;

abstract class ParserFactory {

	use StrictObject;

	private const PARSER_CLASS = __NAMESPACE__ . '\\Compiled\\PrimiParser';
	private const GRAMMAR_FILE = __DIR__ . '/Grammar/Primi.peg';
	private const TARGET_PARSER_FILE = __DIR__ . '/Compiled/PrimiParser.php';
	private const VERSION_FILE = __DIR__ . '/Compiled/version';

	/**
	 * Recompile only once per runtime to avoid expensive checks on each use.
	 * If the grammar changes, the client just needs to restart the process.
	 */
	private static bool $recompiled = \false;

	public static function getParserClass(): string {

		// If we're running inside Phar, we assume the parser already is
		// compiled in the newest version (also, compiling it again - within the
		// Phar - would use file_get_contents to write the result, which locks
		// the file for writing, and that results in "Exclusive locks may only
		// be set for regular files" PHP error).
		if (self::$recompiled === \true || EnvInfo::isRunningInPhar()) {
			return self::PARSER_CLASS;
		}

		$grammarVersion = \md5((string) \filemtime(self::GRAMMAR_FILE));
		$parserVersion = \file_exists(self::VERSION_FILE)
			? \file_get_contents(self::VERSION_FILE)
			: \false;

		if ($grammarVersion !== $parserVersion) {
			self::recompileParser($grammarVersion);
		}

		self::$recompiled = \true;
		return self::PARSER_CLASS;

	}

	private static function recompileParser(string $newVersion): void {

		$grammar = \file_get_contents(self::GRAMMAR_FILE);
		$code = Compiler::compile($grammar);

		\file_put_contents(self::TARGET_PARSER_FILE, $code, \LOCK_EX);
		\file_put_contents(self::VERSION_FILE, $newVersion);

	}

}
