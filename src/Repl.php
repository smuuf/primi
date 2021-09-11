<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use \Smuuf\Primi\Scope;
use \Smuuf\Primi\Ex\ErrorException;
use \Smuuf\Primi\Ex\EngineException;
use \Smuuf\Primi\Ex\SystemException;
use \Smuuf\Primi\Code\Source;
use \Smuuf\Primi\Values\NullValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\ModuleValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Colors;
use \Smuuf\Primi\Helpers\Wrappers\ContextPushPopWrapper;
use \Smuuf\Primi\Drivers\ReadlineUserIoDriver;
use \Smuuf\Primi\Drivers\UserIoDriverInterface;

use \Smuuf\StrictObject;

class Repl {

	use StrictObject;

	/**
	 * @const string How this REPL identifies itself in call stack.
	 */
	private const REPL_NAME_FORMAT = "<repl: %s>";

	private const HISTORY_FILE = '.primi_history';
	private const PRIMARY_PROMPT = '>>> ';
	private const MULTILINE_PROMPT = '... ';

	private const PHP_ERROR_HEADER = "PHP ERROR";
	private const ERROR_REPORT_PLEA =
		"This is probably a bug in Primi or any of its components. "
		. "Please report this to the maintainer.";

	/** @var string Full path to readline history file. */
	private static $historyFilePath;

	/** @var string REPL identifies itself in callstack with this string. */
	protected $replName;

	/**
	 * IO driver used for user input/output.
	 *
	 * This is handy for our unit tests, so we can simulate user input and
	 * gather REPL output.
	 *
	 * @var UserIoDriverInterface
	 */
	protected $driver;

	/**
	 * If `false`, extra "user-friendly info" is not printed out.
	 * This is also handy for our unit tests - less output to test.
	 *
	 * @var bool
	 */
	public static $noExtras = false;

	public function __construct(
		?string $replName = null,
		UserIoDriverInterface $driver = null
	) {

		self::$historyFilePath = getenv("HOME") . '/' . self::HISTORY_FILE;

		$this->replName = sprintf(self::REPL_NAME_FORMAT, $replName ?? 'cli');
		$this->driver = $driver ?? new ReadlineUserIoDriver;

		$this->loadHistory();

	}

	protected function printHelp() {

		$this->driver->output(Colors::get("\n".
			"{green}Use '{_}exit{green}' to exit REPL or '{_}exit!{green}' " .
			"to terminate the process.\n" .
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
	public function start(?Context $ctx = null): ?AbstractValue {

		// If context was not provided, create and use a new one (context
		// will create a new scope if it also was not provided).
		if (!$ctx) {
			$scope = new Scope;
			$services = new InterpreterServices(Config::buildDefault());
			$module = new ModuleValue('__main__', '', $scope);
			$ctx = new Context($services, getcwd());
		} else {
			$module = $ctx->getCurrentModule();
			$scope = $ctx->getCurrentScope();
		}

		// Print out level (position in call stack).
		if (!self::$noExtras) {
			$this->driver->output(self::getLevelInfo($ctx, true));
			$this->printHelp();
		}

		$frame = new StackFrame($this->replName, $module);

		$wrapper = new ContextPushPopWrapper($ctx, $frame, $scope);
		$retval = $wrapper->wrap(function($ctx) {
			return $this->loop($ctx);
		});

		$this->saveHistory();

		if (!self::$noExtras) {
			$this->driver->output(Colors::get("{yellow}Exiting REPL...{_}\n"));
		}

		return $retval;

	}

	private function loop(Context $ctx): ?AbstractValue {

		$scope = $ctx->getCurrentScope();

		$cellNumber = 1;
		$retval = null;

		readline_completion_function(function() use ($scope) {
			return array_keys($scope->getVariables(true));
		});

		while (true) {

			$this->driver->output("\n");
			$input = $this->gatherLines($ctx);

			if (trim($input) && $input !== 'exit') {
				$this->driver->addToHistory($input);
			}

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
					// Return the result of last expression executed, or null.
					return $retval;
				case 'exit!':
					// Catch a non-command 'exit!'.
					// Just quit the whole thing.
					die(1);
			}

			$this->saveHistory();
			$source = new Source($input);

			try {

				// May throw syntax error - will be handled by the catch
				// below.
				$ast = $ctx->getAstProvider()->getAst($source, false);
				$result = DirectInterpreter::execute($ast, $ctx);

				// Store the result into _ variable for quick'n'easy
				// retrieval.
				$scope->setVariable('_', $result);
				$this->printResult($result);

			} catch (ErrorException|SystemException $e) {

				$this->driver->output(Colors::get("{red}ERR: "));
				$this->driver->output($e->getMessage());

			} catch (EngineException|\Throwable $e) {

				// All exceptions other than ErrorException are like to be a
				// problem with Primi or PHP - print the whole PHP exception.
				$this->printPhpTraceback($e);

			}

			$cellNumber++;

		}

	}

	/**
	 * Pretty-prints out a result of a Primi expression. No result or null
	 * values are not to be printed.
	 */
	private function printResult(?AbstractValue $result = null): void {

		// Do not print out empty or NullValue results.
		if ($result === null || $result instanceof NullValue) {
			return;
		}

		$this->printValue($result);

	}

	/**
	 * Pretty-prints out a AbstractValue representation with type info.
	 */
	private function printValue(AbstractValue $result): void {

		$this->driver->output(sprintf(
			"%s %s\n",
			$result->getStringRepr(),
			!self::$noExtras ? self::formatType($result) : null
		));

	}

	/**
	 * Pretty-prints out all variables of a scope (with or without variables
	 * from parent scopes).
	 */
	private function printScope(Scope $c, bool $includeParents): void {

		foreach ($c->getVariables($includeParents) as $name => $value)  {
			$this->driver->output(Colors::get("{lightblue}$name{_}: "));
			$this->printValue($value);
		}

	}

	/**
	 * Pretty-prints out traceback from a context.
	 */
	private function printTraceback(Context $ctx): void {

		$tbString = Func::get_traceback_as_string($ctx->getCallStack(), true);
		$this->driver->output($tbString);

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
			if (!self::$noExtras && $levelInfo) {
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

	/**
	 * Return string with human-friendly information about current level
	 * of nested calls (that is the number of entries in the call stack).
	 */
	private static function getLevelInfo(
		Context $ctx,
		bool $full = false
	): string {

		$level = count($ctx->getCallStack());

		// Do not print anything for level 1 (or less, lol).
		if ($level < 2) {
			return '';
		}

		$out = "L +{$level}";
		if ($full) {
			$out = "Entering level {$out}.\n";
		}

		return Colors::get("{darkgrey}{$out}{_}");

	}

	private static function formatType(AbstractValue $value) {

		return Colors::get(sprintf(
			"{darkgrey}(%s %s){_}",
			$value->getTypeName(),
			Func::object_hash($value)
		));

	}

}
