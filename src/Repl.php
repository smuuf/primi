<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\NullValue;
use \Smuuf\Primi\Colors;
use \Smuuf\Primi\Interpreter;
use \Smuuf\Primi\IContext;
use \Smuuf\Primi\IReadlineDriver;
use \Smuuf\Primi\Ex\BaseException;

use function \Smuuf\Primi\Helpers\object_hash as primifn_object_hash;

class Repl extends \Smuuf\Primi\StrictObject {

	const HISTORY_FILE = '.primi_history';
	const PRIMARY_PROMPT = '>>> ';
	const MULTILINE_PROMPT = '... ';

	private const PHP_ERROR_HEADER = "PHP ERROR:";
	private const ERROR_REPORT_PLEA = "This is probably a bug in Primi or any of "
		. "its components. Please report this to the maintainer.";

	/** @var string Full path to readline history file. */
	private $historyFilePath;

	/** @var Interpreter */
	protected $interpreter;

	/** @var IReadlineDriver */
	protected $driver;

	/** @var bool */
	protected $rawOutput = false;

	public function __construct(
		Interpreter $interpreter,
		IReadlineDriver $driver = null,
		bool $rawOutput = false
	) {

		self::printHelp();

		$this->interpreter = $interpreter;
		$this->rawOutput = $rawOutput;
		$this->driver = $driver ?: new \Smuuf\Primi\Readline;
		$this->historyFilePath = getenv("HOME") . '/' . self::HISTORY_FILE;
		$this->loadHistory();

	}

	protected static function printHelp() {

		echo Colors::get("\n".
			"{green}Use '{_}exit{green}' to exit.\n" .
			"Use '{_}?{green}' to view local variables " .
			"or '{_}??{green}' to view all variables.\n" .
			"The latest result is stored in '{_}_{green}' variable.\n\n"
		);

	}

	public function start() {

		// Allow saving history even on serious errors.
		register_shutdown_function(function(): void {
			$this->saveHistory();
		});

		$this->loop();

	}

	private function loop() {

		$i = $this->interpreter;
		$c = $i->getContext();

		readline_completion_function(function() use ($c) {
			return array_keys($c->getVariables());
		});

		while (true) {

			$input = $this->gatherLines();

			switch (trim($input)) {
				case '?':
					// Print defined variables.
					$this->printContext($c, false);
					continue 2;
				case '??':
					// Print all variables, including global ones provided by
					// extensions.
					$this->printContext($c, true);
					continue 2;
				case '':
					// Ignore (skip) empty input.
					continue 2;
				case 'exit':
					// Catch a non-command 'exit'.
					break 2;
			}

			$this->driver->readlineAddHistory($input);

			try {

				// Ensure that there's a semicolon at the end.
				// This way users won't have to put it in there themselves.
				$result = $i->run("$input;");

				// Store the result into _ variable for quick'n'easy retrieval.
				$c->setVariable('_', $result);

				$this->printResult($result);
				echo "\n";

			} catch (BaseException $e) {
				echo Colors::get("{red}ERR:{_} {$e->getMessage()}\n");
			} catch (\Throwable $e) {
				$this->printPhpTraceback($e);
			}

		}

	}

	private function printResult(Value $result = null): void {

		// Do not print empty or NullValue results.
		if ($result === null || $result instanceof NullValue) {
			return;
		}

		printf(
			"%s %s\n",
			$result->getStringRepr(),
			!$this->rawOutput ? self::formatType($result) : null
		);

	}

	private static function formatType(Value $value) {

		return Colors::get(sprintf(
			"{darkgrey}(%s %s){_}",
			$value::TYPE,
			primifn_object_hash($value)
		));

	}

	private function printContext(IContext $c, bool $includeGlobals): void {

		foreach ($c->getVariables($includeGlobals) as $name => $value)  {
			echo "$name: ";
			$this->printResult($value);
		}

	}

	private static function printPhpTraceback(\Throwable $e) {

		$msg = Colors::get(sprintf("\n{white}{-red}%s", self::PHP_ERROR_HEADER));
		$msg .= " {$e->getMessage()} @ {$e->getFile()}:{$e->getLine()}\n";
		echo $msg;

		// Best and easiest to get version of backtrace I can think of.
		echo $e->getTraceAsString();
		echo Colors::get(sprintf("\n{yellow}%s\n", self::ERROR_REPORT_PLEA));

	}

	private function gatherLines(): string {

		$gathering = false;
		$lines = '';

		while (true) {

			if ($gathering === false) {
				$prompt = self::PRIMARY_PROMPT;
			} else {
				$prompt = self::MULTILINE_PROMPT;
			}

			$input = $this->driver->readline($prompt);
			[$incomplete, $trim] = self::isIncompleteInput($input);

			if ($incomplete) {

				// Consider non-empty line ending with a "\" character as
				// a part of multiline input. That is: Trim the backslash and
				// go read another line from the user.
				$lines .= mb_substr($input, 0, mb_strlen($input) - $trim) . "\n";
				$gathering = true;

			} else {

				// Normal non-multiline input. Add it to the line buffer and
				// return the whole buffer (with all lines that may have been)
				// gathered as multiline input so far.
				$lines .= $input;
				return $lines;

			}

		}

	}

	private function isIncompleteInput(string $input) {

		if (empty(trim($input))) {
			return [false, 0];
		}

		// Lines ending with opening curly brackets are considered incomplete.
		if ($input[-1] === "{") {
			return [true, 0];
		}

		// Lines ending with backslashes are considered incomplete.
		// And such backslashes at the EOL are to be trimmed from real input.
		if ($input[-1] === '\\') {
			return [true, 1];
		}

		// Lines starting with a SPACE or a TAB are considered incomplete.
		if (strspn($input, "\t ") !== 0) {
			return [true, 0];
		}

	}

	private function loadHistory() {

		if (is_readable($this->historyFilePath)) {
			$this->driver->readlineReadHistory($this->historyFilePath);
		}

	}

	private function saveHistory() {

		if (is_writable(dirname($this->historyFilePath))) {
			$this->driver->readlineWriteHistory($this->historyFilePath);
		}

	}

}
