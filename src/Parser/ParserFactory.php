<?php

namespace Smuuf\Primi\Parser;

use \hafriedlander\Peg\Compiler;

use \Smuuf\StrictObject;

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
	private static $recompiled = \false;

	public static function getParserClass(): string {

		if (self::$recompiled === \true) {
			return self::PARSER_CLASS;
		}

		$grammarVersion = \md5(\filemtime(self::GRAMMAR_FILE));
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
