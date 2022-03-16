<?php

declare(strict_types=1);

namespace Smuuf\Primi\Cli;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Repl;
use \Smuuf\Primi\Logger;
use \Smuuf\Primi\Config;
use \Smuuf\Primi\EnvInfo;
use \Smuuf\Primi\Interpreter;
use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Ex\BaseException;
use \Smuuf\Primi\Code\Source;
use \Smuuf\Primi\Code\SourceFile;
use \Smuuf\Primi\Code\AstProvider;
use \Smuuf\Primi\Ex\InternalSyntaxError;
use \Smuuf\Primi\Ex\SyntaxError;
use \Smuuf\Primi\Helpers\Stats;
use \Smuuf\Primi\Helpers\Colors;
use \Smuuf\Primi\Location;
use \Smuuf\Primi\Parser\ParserHandler;

class Entrypoint {

	use StrictObject;

	/** @var array<string, mixed> */
	private array $config = [
		// Print only parsed AST and then exit.
		'only_tree' => false,
		// Parse the input (build AST), print parser stats and exit.
		'print_runtime_stats' => false,
		// After the script finishes, print out contents of main global scope.
		'print_scope' => false,
		// After the script finishes, print out contents of main global scope.
		'parser_stats' => false,
		// The default argument represents Primi code to run.
		'input' => false,
		// Print verbose logging to stderr.
		'verbose' => false,
		// True if the input is not a Primi file to run, but just a string
		// containing Primi source code to run.
		'input_is_code' => false,
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
			throw new \ErrorException($message, 0, $severity, $file, $line);
		}, E_ALL);

		set_exception_handler(function($ex) {
			echo "PHP ERROR " . get_class($ex);
			echo ": {$ex->getMessage()} @ {$ex->getFile()}:{$ex->getLine()}\n";
			echo $ex->getTraceAsString() . "\n";
		});

	}

	public function execute(): void {

		$cfg = $this->config;

		// Enable verbose logging.
		if ($cfg['verbose']) {
			Logger::enable();
		}

		// Enable stats gathering, if requested.
		if ($cfg['print_runtime_stats']) {

			Stats::enable();
			// Try printing stats at the absolute end of runtime.
			register_shutdown_function(fn() => Stats::print());

		}

		// Determine the source. Act as REPL if no source was specified.
		if (empty($cfg['input'])) {

			echo $this->getHeaderString('REPL');
			$repl = new Repl;
			$repl->start();
			die;

		}

		if ($cfg['input_is_code']) {

			// Input is passed as a string of Primi source code.
			$source = new Source($cfg['input']);

		} else {

			try {
				$filepath = $cfg['input'];
				$source = new SourceFile($filepath);
			} catch (EngineError $e) {
				self::errorExit($e->getMessage());
			}

		}

		if ($cfg['parser_stats'] || $cfg['only_tree']) {

			$sourceCode = $source->getSourceCode();
			$ph = new ParserHandler($source->getSourceCode());

			// Run parser and catch any error that may have occurred.
			try {

				try {
					$tree = $ph->run();
				} catch (InternalSyntaxError $e) {

					// Show a bit of code where the syntax error occurred.
					$excerpt = \mb_substr($sourceCode, $e->getErrorPos(), 20);

					throw new SyntaxError(
						new Location($source->getSourceId(), $e->getErrorLine()),
						$excerpt,
						$e->getReason()
					);

				}

			} catch (BaseException $e) {
				self::errorExit("{$e->getMessage()}");
			}

			// If requested, just get the syntax tree and die.
			if ($cfg['only_tree']) {
				print_r($tree);
				die;
			}

			echo "Parser stats:\n";
			foreach ($ph->getStats() as $name => $value) {
				$value = round($value, 4);
				echo "- {$name}: {$value} s\n";
			}
			die;

		}

		// Create interpreter.
		$config = Config::buildDefault();
		$config->addImportPath(getcwd());
		$interpreter = new Interpreter($config);

		// Run interpreter and catch any error that may have occurred.
		try {
			$mainScope = $interpreter->run($source);
		} catch (BaseException $e) {
			self::errorExit("{$e->getMessage()}");
		}

		if ($cfg['print_scope']) {
			foreach ($mainScope->getVariables() as $name => $value) {
				echo "$name: {$value->getStringRepr()}\n";
			}
			die;
		}

	}

	/**
	 * @return never
	 */
	private static function errorExit(string $text): void {
		echo "$text\n";
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
				case "-t":
				case "--tree":
					$cfg['only_tree'] = true;
				break;
				case "-pst":
				case "--parser-stats":
					$cfg['parser_stats'] = true;
				break;
				case "-s":
				case "--source":
					$cfg['input_is_code'] = true;
				break;
				case "-ps":
				case "--print-scope":
					$cfg['print_scope'] = true;
				break;
				case "-rst":
					case "--runtime-stats":
						$cfg['print_runtime_stats'] = true;
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
			primi -ps ./some_file.primi
			primi -s 'something = 1; print(something + 1)'
			primi -rst -s 'something = 1; print(something + 1)'
		{green}Options:{_}
			{yellow}-h, --help{_}
				Print this help.
			{yellow}-ps, --print-scope{_}
				Print contents of global scope after execution.
			{yellow}-pst, --parser-stats{_}
				Print parser stats upon exit (code is not executed).
			{yellow}-rst, --stats{_}
				Print interpreter runtime stats upon exit.
			{yellow}-s, --source{_}
				Treat <input file> as string instead of a source file path.
			{yellow}-t, --tree{_}
				Only print syntax tree and exit.
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
