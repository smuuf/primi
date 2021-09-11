<?php

declare(strict_types=1);

namespace Smuuf\Primi\Cli;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Repl;
use \Smuuf\Primi\Logger;
use \Smuuf\Primi\Config;
use \Smuuf\Primi\Interpreter;
use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Ex\BaseException;
use \Smuuf\Primi\Code\Source;
use \Smuuf\Primi\Code\SourceFile;
use \Smuuf\Primi\Parser\ParserHandler;
use \Smuuf\Primi\Helpers\Colors;

class Entrypoint {

	use StrictObject;

	private $config = [
		// Print only parsed AST and then exit.
		'only_tree' => false,
		// Parse the input (build AST), print parser stats and exit.
		'print_process_stats' => false,
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

	/** Root dir path where entrypoint operates. */
	private string $rootDir;

	/**
	 * @param array $args Arguments to the CLI script (without the first $0
	 * argument).
	 */
	public function __construct(array $args, string $rootDir) {

		self::init();

		$this->config = $this->parseArguments($this->config, $args);
		$this->rootDir = $rootDir;

	}

	private static function init() {

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
		if ($cfg['print_process_stats']) {
			Stats::enable();
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

			$ph = new ParserHandler($source->getSourceCode());

			// Run parser and catch any error that may have occurred.
			try {
				$tree = $ph->run();
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
				echo "- {$name}: {$value}\n";
			}
			die;

		}

		// Create interpreter.
		$config = Config::buildDefault();
		$interpreter = new Interpreter($config);

		// Run interpreter and catch any error that may have occurred.
		try {
			$context = $interpreter->run($source);
		} catch (BaseException $e) {
			self::errorExit("{$e->getMessage()}");
		}

		if ($cfg['print_scope']) {
			$scope = $context->getCurrentScope();
			foreach ($scope->getVariables() as $name => $value) {
				echo "$name: {$value->getStringRepr()}\n";
			}
			die;
		}

	}

	private static function errorExit(string $text): void {
		echo "$text\n";
		die(1);
	}

	private function parseArguments(array $defaults, array $args): array {

		$cfg = $defaults;

		while ($a = array_shift($args)) {
			switch ($a) {
				case "-h":
				case "--help":
					self::dieWithHelp();
				break;
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
				case "-st":
					case "--stats":
						$cfg['print_process_stats'] = true;
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

	private static function dieWithHelp(): void {

		$header = self::getHeaderString('CLI');
		$help = <<<HELP
		$header
		Usage: primi [<options>] [<input file>]
		Options:
		-t, --tree
			Only print syntax tree and exit.
		-s, --source
			Treat <input file> as string instead of a source file path.
		-ps, --print-scope
			Print contents of global scope after execution.
		-st, --stats
			Print interpreter stats upon exit.

		HELP;

		die($help);

	}

	private static function getHeaderString(string $env = null): string {
		$env = $env ? "($env)" : null;
		$php = PHP_VERSION;

		$string = "Primi {$env}, Copyright (c) Premysl Karbula\n"
			. "{yellow}Running on PHP {$php}{_}\n";
		return Colors::get($string);

	}

}
