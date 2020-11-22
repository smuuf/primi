<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use \Smuuf\Primi\Colors;
use \Smuuf\Primi\AbstractScope;
use \Smuuf\Primi\ICliIoDriver;
use \Smuuf\Primi\Ex\SyntaxError;
use \Smuuf\Primi\Ex\BaseException;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\NullValue;

class Repl extends \Smuuf\Primi\StrictObject {

	private const DEFAULT_REPL_ID = "<repl>";

	private const HISTORY_FILE = '.primi_history';
	private const PRIMARY_PROMPT = '>>> ';
	private const MULTILINE_PROMPT = '... ';

	private const PHP_ERROR_HEADER = "PHP ERROR:";
	private const ERROR_REPORT_PLEA =
		"This is probably a bug in Primi or any "
		. "of its components. Please report this to the maintainer.";

	/** @var string Full path to readline history file. */
	private static $historyFilePath;

	/** @var string REPL identifies itself in callstack with this string. */
	protected $replId;

	/**
	 * IO driver used for user input/output.
	 *
	 * This is handy for our unit tests, so we can simulate user input and
	 * gather REPL output.
	 *
	 * @var ICliIoDriver
	 */
	protected $driver;

	/**
	 * If `true`, values are printed out without their unique ID.
	 * This is handy for our unit tests, because unique IDs are pretty random
	 * and that makes it complicated to test stable output.
	 *
	 * @var bool
	 */
	protected $printRawValues = false;

	public function __construct(
		?string $replId = null,
		ICliIoDriver $driver = null,
		bool $printRawValues = false
	) {

		self::$historyFilePath = getenv("HOME") . '/' . self::HISTORY_FILE;

		$this->printRawValues = $printRawValues;
		$this->replId = $replId ?? self::DEFAULT_REPL_ID;
		$this->driver = $driver ?? new \Smuuf\Primi\ReadlineCliIoDriver;

	}

	protected function printHelp() {

		$this->driver->output(Colors::get("\n".
			"{green}Use '{_}exit{green}' to exit.\n" .
			"Use '{_}?{green}' to view local variables, " .
			"'{_}??{green}' to view all variables, " .
			"'{_}?tb{green}' to see traceback. \n" .
			"The latest result is stored in '{_}_{green}' variable.\n"
		));

	}

	/**
	 * Main REPL entrypoint.
	 *
	 * Creates a new instance of interpreter and runs it with a Context, if it
	 * was specified as argument. Otherwise the interpreter creates its own
	 * new context and REPL operates within that one.
	 */
	public function start(?Context $ctx = null): ?Value {

		$this->printHelp();
		$this->loadHistory();

		// Allow saving history even on serious errors.
		register_shutdown_function(function(): void {
			$this->saveHistory();
		});

		// If context was not provided, create and use a new one.
		$ctx = $ctx ?? new Context(new Scope);
		$ctx->pushCall($this->replId);

		// Print out level (position in call stack).
		$this->driver->output(self::getLevelInfo($ctx, true));
		$retval = $this->loop(new RawInterpreter, $ctx);

		return $retval;

	}

	private function loop(RawInterpreter $intepreter, Context $ctx): ?Value {

		$scope = $ctx->getCurrentScope();

		readline_completion_function(function() use ($scope) {
			return array_keys($scope->getVariables(true));
		});

		$lastValidInput = false;
		while (true) {

			// Add last valid input into history.
			// Doing it at this point allows us to easily do both:
			// a) Skip adding expressions to history if it resulted in syntax
			// error.
			// b) Add control commands (the `switch` statement below) to
			// history without having to call `addToHistory` after every `case`.
			if ($lastValidInput !== false) {
				$this->driver->addToHistory($lastValidInput);
			}

			$this->driver->output("\n");
			$input = $this->gatherLines($ctx);
			$lastValidInput = $input;

			switch (trim($input)) {
				case '?':
					// Print defined variables.
					$this->printScope($scope, false);
					continue 2;
				case '?tb':
					// Print traceback
					$this->printTraceback($ctx);
					continue 2;
				case '??':
					// Print all variables, including the ones from parent
					// scopes (i.e. even from extensions).
					$this->printScope($scope, true);
					continue 2;
				case '':
					// Ignore (skip) empty input.
					continue 2;
				case 'exit':
					// Catch a non-command 'exit'.
					break 2;
			}

			try {

				// Ensure that there's a semicolon at the end.
				// This way users won't have to put it in there themselves.
				$result = $intepreter->execute("$input;", $ctx);

				// Store the result into _ variable for quick'n'easy retrieval.
				$scope->setVariable('_', $result);

				$this->printResult($result);

			} catch (BaseException $e) {

				$this->driver->output(
					Colors::get("{red}ERR:{_} {$e->getMessage()}\n")
				);

				// Input resulted in syntax error - won't be added to history.
				if ($e instanceof SyntaxError) {
					$lastValidInput = false;
				}

			} catch (\Throwable $e) {
				$this->printPhpTraceback($e);
			}

		}

		// Return the result of last expression executed, or null.
		return $result ?? null;

	}

	/**
	 * Pretty-prints out a result of a Primi expression. No result or null
	 * values are not to be printed.
	 */
	private function printResult(?Value $result = null): void {

		// Do not print out empty or NullValue results.
		if ($result === null || $result instanceof NullValue) {
			return;
		}

		$this->printValue($result);

	}

	/**
	 * Pretty-prints out a Value representation with type info.
	 */
	private function printValue(Value $result): void {

		$this->driver->output(sprintf(
			"%s %s\n",
			$result->getStringRepr(),
			!$this->printRawValues ? self::formatType($result) : null
		));

	}

	/**
	 * Pretty-prints out all variables of a scope (with or without variables
	 * from parent scopes).
	 */
	private function printScope(AbstractScope $c, bool $includeParents): void {

		foreach ($c->getVariables($includeParents) as $name => $value)  {
			$this->driver->output(Colors::get("{lightblue}$name{_}: "));
			$this->printValue($value);
		}

	}

	/**
	 * Pretty-prints out traceback from a context.
	 */
	private function printTraceback(Context $ctx): void {

		$this->driver->output(Colors::get(
			"{yellow}Traceback:{_}\n"
		));

		foreach ($ctx->getTraceback() as $level => $callId)  {
			$this->driver->output(Colors::get(
				"{yellow}[$level]{_} $callId\n"
			));
		}

	}

	/**
	 * Pretty-prints out traceback from a PHP exception.
	 */
	private function printPhpTraceback(\Throwable $e) {

		$type = get_class($e);
		$msg = Colors::get(sprintf("\n{white}{-red}%s", self::PHP_ERROR_HEADER));
		$msg .= " $type: {$e->getMessage()} @ {$e->getFile()}:{$e->getLine()}\n";
		$this->driver->output($msg);

		// Best and easiest to get version of backtrace I can think of.
		$this->driver->output($e->getTraceAsString());
		$this->driver->output(
			Colors::get(sprintf("\n{yellow}%s\n", self::ERROR_REPORT_PLEA))
		);

	}

	/**
	 * Gathers and returns user input for REPL.
	 *
	 * Uses a "very sophisticated" way of allowing the user to enter multi-line
	 * input.
	 */
	private function gatherLines(Context $ctx): string {

		$gathering = false;
		$lines = '';
		$levelInfo = self::getLevelInfo($ctx);

		while (true) {

			if ($gathering === false) {
				$prompt = self::PRIMARY_PROMPT;
			} else {
				$prompt = self::MULTILINE_PROMPT;
			}

			// Display level of nesting - number of items in current call stack.
			if ($levelInfo) {
				$this->driver->output("{$levelInfo}\n");
			}

			$input = $this->driver->input($prompt);
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

	private static function isIncompleteInput(string $input) {

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

		if (is_readable(self::$historyFilePath)) {
			$this->driver->loadHistory(self::$historyFilePath);
		}

	}

	private function saveHistory() {

		if (is_writable(dirname(self::$historyFilePath))) {
			$this->driver->storeHistory(self::$historyFilePath);
		}

	}


	private static function getLevelInfo(
		Context $ctx,
		bool $full = false
	): string {

		$level = count($ctx->getCallStack());

		if ($level === 0) {
			return '';
		}

		$out = "L +{$level}";
		if ($full) {
			$out = "Entering level {$out}.\n";
		}

		return Colors::get("{darkgrey}{$out}{_}");

	}

	private static function formatType(Value $value) {

		return Colors::get(sprintf(
			"{darkgrey}(%s %s){_}",
			$value::TYPE,
			Func::object_hash($value)
		));

	}

}
