<?php

declare(strict_types=1);

namespace Smuuf\Primi\Cli;

use Smuuf\StrictObject;
use Smuuf\Primi\Repl;
use Smuuf\Primi\Logger;
use Smuuf\Primi\Config;
use Smuuf\Primi\EnvInfo;
use Smuuf\Primi\Interpreter;
use Smuuf\Primi\Ex\EngineError;
use Smuuf\Primi\Ex\UncaughtError;
use Smuuf\Primi\Code\Source;
use Smuuf\Primi\Code\SourceFile;
use Smuuf\Primi\Code\BytecodeDumper;
use Smuuf\Primi\Code\BytecodeProvider;
use Smuuf\Primi\Helpers\Stats;
use Smuuf\Primi\Helpers\Colors;
use Smuuf\Primi\Helpers\Exceptions;

class Entrypoint {

	use StrictObject;

	/** @var array<string, mixed> */
	private array $config = [
		// Print only parsed AST and then exit.
		'only_ast' => false,
		// Print only compiled bytecode and then exit.
		'only_dis' => false,
		// Only print compiled bytecode instruction stats.
		'only_dis_stats' => false,
		// The default argument represents Primi code to run.
		'input' => false,
		// Print verbose logging to stderr.
		'verbose' => false,
		// True if the input is not a Primi file to run, but just a string
		// containing Primi source code to run.
		'input_is_code' => false,
		// Forces bytecode cache to be disabled.
		'no_cache' => false,
	];

	/**
	 * @param array<string> $args Arguments to the CLI script (without the
	 * first $0 argument).
	 */
	public function __construct(array $args) {

		self::globalInit();
		$this->config = $this->parseArguments($this->config, $args);

	}

	private static function globalInit(): void {

		error_reporting(E_ALL);
		set_error_handler(function($severity, $message, $file, $line) {

			// This error code is not included in error_reporting, so let it fall
			// through to the standard PHP error handler.
			if (!(error_reporting() & $severity)) {
				return false;
			}

			throw new \ErrorException($message, 0, $severity, $file, $line);

		}, E_ALL);

		set_exception_handler(function($ex) {
			echo "PHP ERROR " . get_class($ex);
			echo ": {$ex->getMessage()} @ {$ex->getFile()}:{$ex->getLine()}\n";
			echo $ex->getTraceAsString() . "\n";
		});

		try {
			EnvInfo::bootCheck();
		} catch (EngineError $e) {
			self::errorExit($e->getMessage());
		}

	}

	public function execute(): void {

		$cfg = $this->config;

		// Enable verbose logging.
		if ($cfg['verbose']) {
			Logger::enable();
		}

		// Determine the source. Act as REPL if no source was specified.
		if (empty($cfg['input'])) {
			$this->runRepl();
			return;
		}

		if ($cfg['input_is_code']) {
			// If the input is passed as a string of Primi source code, consider
			// the current working directory as runtime's main directory.
			$source = new Source($cfg['input']);
			$mainDir = getcwd();
		} else {
			try {
				$filepath = $cfg['input'];
				$source = new SourceFile($filepath);
				$mainDir = $source->getDirectory();
			} catch (EngineError $e) {
				self::errorExit($e->getMessage());
			}
		}

		$config = Config::buildDefault();

		if ($cfg['no_cache']) {
			$config->setTempDir(null);
		}

		$config->addImportPath($mainDir);

		// Create interpreter.
		try {
			$interpreter = new Interpreter($config);
		} catch (EngineError $e) {
			self::errorExit($e->getMessage());
		}

		if ($cfg['only_ast']) {
			$ast = BytecodeProvider::parse($source);
			print_r($ast);
			return;
		}

		if ($cfg['only_dis']) {
			$code = BytecodeProvider::compile($source);
			BytecodeDumper::dumpBytecode($code);
			return;
		}

		if ($cfg['only_dis_stats']) {
			$code = BytecodeProvider::compile($source);
			BytecodeDumper::dumpBytecodeStats($code);
			return;
		}

		// Run interpreter and catch any error that may have occurred.
		try {
			$interpreter->run($source);
		} catch (UncaughtError $uncaught) {
			$thrown = $uncaught->thrownException;
			Term::stderr(Exceptions::renderThrownExceptionToText($thrown));
			die(1);
		}

	}

	private function runRepl(): void {

		Term::stderr($this->getHeaderString('REPL'));
		$repl = new Repl;
		$repl->start();

	}

	/**
	 * @return never
	 */
	private static function errorExit(string $text): void {
		Term::stderr(Term::error($text));
		die(1);
	}

	/**
	 * @param array<string, mixed> $defaults
	 * @param array<string, mixed> $args
	 * @return array<string, mixed>
	 */
	private function parseArguments(array $defaults, array $args): array {

		$cfg = $defaults;

		while ($a = array_shift($args)) {
			switch ($a) {
				case "-h":
				case "--help":
					self::dieWithHelp();
				case "--ast":
					$cfg['only_ast'] = true;
					break;
				case "--dis":
					$cfg['only_dis'] = true;
					break;
				case "--dis-stats":
					$cfg['only_dis_stats'] = true;
					break;
				case "--no-cache":
					$cfg['no_cache'] = true;
					break;
				case "-s":
				case "--source":
					$cfg['input_is_code'] = true;
					break;
				case "-v":
				case "--verbose":
					$cfg['verbose'] = true;
					break;
				default:
					$cfg['input'] = $a;
				break;
			}
		}

		return $cfg;

	}

	/**
	 * @return never
	 */
	private static function dieWithHelp(): void {

		$header = self::getHeaderString('CLI');
		$help = <<<HELP
		$header
		{green}Usage:{_}
			primi [<options>] [<input file>]
		{green}Examples:{_}
			primi ./some_file.primi
			primi -s 'something = 1; print(something + 1)'
			primi --dis -s 'something = 1; print(something + 1)'
		{green}Options:{_}
			{yellow}-h, --help{_}
				Print this help.
			{yellow}-s, --source{_}
				Treat <input file> as string instead of a source file path.
			{yellow}--ast{_}
				Dump abstract syntax tree parsed from the input.
			{yellow}--dis{_}
				Dump opcodes from the compiled input.
			{yellow}--dis-stats{_}
				Dump opcodes stats from the compiled input.
			{yellow}--no-cache{_}
				Disable parser/compiler cache.
			{yellow}-v, --verbose{_}
				Enable verbose debug logging.

		HELP;

		die(Colors::get($help));

	}

	private static function getHeaderString(string $env = null): string {

		$env = $env ? "($env)" : null;
		$php = PHP_VERSION;
		$buildInfo = EnvInfo::getPrimiBuild();

		$string = "Primi {$env}, Copyright (c) Premysl Karbula "
			. "{darkgrey}(build {$buildInfo}){_}\n"
			. "{yellow}Running on PHP {$php}{_}\n";
		return Colors::get($string);

	}

}
