<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use Smuuf\StrictObject;
use Smuuf\Primi\Scope;
use Smuuf\Primi\Ex\EngineException;
use Smuuf\Primi\Code\Bytecode;
use Smuuf\Primi\Code\Source;
use Smuuf\Primi\Parser\GrammarHelpers;
use Smuuf\Primi\Values\NullValue;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Values\ModuleValue;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Helpers\Colors;
use Smuuf\Primi\Drivers\ReadlineUserIoDriver;
use Smuuf\Primi\Drivers\ReplIoDriverInterface;
use Smuuf\Primi\Ex\UncaughtError;
use Smuuf\Primi\Helpers\Exceptions;

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

	/** Full path to readline history file. */
	private static string $historyFilePath;

	/** REPL identifies itself in callstack with this string. */
	protected string $replName;

	/**
	 * IO driver used for user input/output.
	 *
	 * This is handy for our unit tests, so we can simulate user input and
	 * gather REPL output.
	 */
	protected ReplIoDriverInterface $ioDriver;

	/**
	 * If `false`, extra "user-friendly info" is not printed out.
	 * This is also handy for our unit tests - less output to test.
	 *
	 * @var bool
	 */
	public static $noExtras = false;

	public function __construct(
		?string $replName = null,
		ReplIoDriverInterface $driver = null
	) {

		self::$historyFilePath = getenv("HOME") . '/' . self::HISTORY_FILE;

		$this->replName = sprintf(self::REPL_NAME_FORMAT, $replName ?? 'cli');
		$this->ioDriver = $driver ?? new ReadlineUserIoDriver;

		$this->loadHistory();

	}

	protected function printHelp(): void {

		$this->ioDriver->stderr(Colors::get("\n".
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
	public function start(?Context $ctx = null): void {

		// If context was not provided, create and use a new one.
		if (!$ctx) {
			$config = Config::buildDefault();
			$config->addImportPath(getcwd());
			$ctx = new Context($config);
		}

		$this->loop($ctx);

	}

	private function loop(Context $ctx): void {

		// Print out level (current frame's index in call stack).
		if (!self::$noExtras) {
			$level = self::getStackLevel($ctx);
			$this->ioDriver->stderr(Colors::get(
				"{darkgrey}Starting REPL at F {$level}{_}"
			));
			$this->printHelp();
		}

		if (!$frame = $ctx->getCurrentFrame()) {

			$scope = new Scope(parent: $ctx->getBuiltins());
			$module = new ModuleValue(MagicStrings::MODULE_MAIN_NAME, '', $scope);
			$frame = $ctx->buildFrame(
				$this->replName,
				bytecode: new Bytecode([]),
				scope: $scope,
				module: $module,
			);

		}

		$scope = $frame->getScope();
		$scopeComp = new ScopeComposite($scope, $ctx->getBuiltins());

		readline_completion_function(
			static fn($buffer) => self::autocomplete($buffer, $scopeComp),
		);

		while (true) {

			// Display frame level - based on current level of call stack.
			$level = self::getStackLevel($ctx);
			if (!self::$noExtras && $level) {
				$this->ioDriver->stderr(Colors::get("{darkgrey}F {$level}{_}"));
			}

			$input = $this->gatherLines($ctx);

			if (trim($input) && $input !== 'exit') {
				$this->ioDriver->addToHistory($input);
			}

			switch (trim($input)) {
				case '?':
					// Print defined variables.
					$this->dumpVariables($scope, includeParents: false);
					continue 2;
				case '??':
					// Print all variables, including the ones from parent
					// scopes and builtins.
					$this->dumpVariables($scope, includeParents: true);
					continue 2;
				case '?tb':
					// Print traceback.
					$this->printTraceback($ctx);
					continue 2;
				case '':
					// Ignore (skip) empty input.
					continue 2;
				case 'exit':
					// Catch a non-command 'exit'.
					// Return the result of last expression executed, or null.
					break 2;
				case 'exit!':
					// Catch a non-command 'exit!'.
					// Just quit the whole thing.
					die(1);
			}

			$this->saveHistory();

			try {

				// May throw syntax error - will be handled by the catch below.
				$result = $ctx->runSource(
					new Source($input),
					$frame,
					compilerArgs: ['keepValue' => true],
				);

				// Store the result into _ variable for quick'n'easy
				// retrieval.
				$frame->getScope()->setVariable('_', $result);
				$this->printResult($result);

			} catch (UncaughtError $uncaught) {
				$thrown = $uncaught->thrownException;
				$excText = Exceptions::renderThrownExceptionToText($thrown);
				$this->ioDriver->stderr($excText);
			} catch (EngineException|\Throwable $e) {

				// All exceptions other than ErrorException are likely to be a
				// problem with Primi or PHP - print the whole PHP exception.
				$this->printPhpTraceback($e);

			}

			$this->ioDriver->stdout("\n");

		}

		$this->saveHistory();

		if (!self::$noExtras) {
			$level = self::getStackLevel($ctx);
			$this->ioDriver->stderr(Colors::get(
				"{yellow}Exiting REPL frame $level{_}"
			));
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

		$this->ioDriver->stdout(self::formatValue($result), "\n");

	}

	/**
	 * Pretty-prints out all variables of a scope (with or without variables
	 * from parent scopes).
	 */
	private function dumpVariables(
		Scope $scope,
		bool $includeParents = false,
	): void {

		foreach ($scope->getVariables($includeParents) as $name => $value) {
			$this->ioDriver->stdout(
				Colors::get("{lightblue}$name{_}: "),
				self::formatValue($value),
				"\n",
			);
		}

		$this->ioDriver->stdout("\n");

	}

	/**
	 * Pretty-prints out traceback from a context.
	 */
	private function printTraceback(Context $ctx): void {

		$tb = Exceptions::unwindStack($ctx);
		$this->ioDriver->stdout(Exceptions::renderTracebackToText($tb), "\n");

	}

	/**
	 * Pretty-prints out traceback from a PHP exception.
	 */
	private function printPhpTraceback(\Throwable $e): void {

		$type = get_class($e);
		$msg = Colors::get(sprintf("\n{white}{-red}%s", self::PHP_ERROR_HEADER));
		$msg .= " $type: {$e->getMessage()} @ {$e->getFile()}:{$e->getLine()}";
		$this->ioDriver->stderr($msg);

		// Best and easiest to get version of backtrace I can think of.
		$this->ioDriver->stderr(
			$e->getTraceAsString(),
			Colors::get(sprintf("\n{yellow}%s", self::ERROR_REPORT_PLEA)),
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

		while (true) {

			if ($gathering === false) {
				$prompt = self::PRIMARY_PROMPT;
			} else {
				$prompt = self::MULTILINE_PROMPT;
			}

			$input = $this->ioDriver->input($prompt);
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

	// Static helpers.

	/**
	 * Returns a pretty string from AbstractValue representation with type info.
	 */
	private static function formatValue(AbstractValue $value): string {

		return sprintf(
			"%s %s",
			$value->getStringRepr(),
			!self::$noExtras ? self::formatType($value) : null,
		);

	}

	/**
	 * Returns a pretty string representing value's type info.
	 */
	private static function formatType(AbstractValue $value): string {

		return Colors::get(sprintf(
			"{darkgrey}(%s %s){_}",
			$value->getTypeName(),
			Func::object_hash($value),
		));

	}


	/**
	 * @return array{bool, int}
	 */
	private static function isIncompleteInput(string $input): array {

		if ($input === '') {
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

		return [false, 0];

	}

	private function loadHistory(): void {

		if (is_readable(self::$historyFilePath)) {
			$this->ioDriver->loadHistory(self::$historyFilePath);
		}

	}

	private function saveHistory(): void {

		if (is_writable(dirname(self::$historyFilePath))) {
			$this->ioDriver->storeHistory(self::$historyFilePath);
		}

	}

	private static function getStackLevel(Context $ctx): int {
		return (int) $ctx->getCurrentFrame()?->callStackSize;
	}

	/**
	 * @return array<string>
	 */
	private static function autocomplete(
		string $buffer,
		ScopeComposite $scopeComp,
	): array {

		$names = [];

		if (!$buffer || GrammarHelpers::isValidName($buffer)) {

			// Buffer (word of input) is empty - or maybe a simple variable.
			$names = array_keys($scopeComp->getVariables(true));

		} elseif (GrammarHelpers::isSimpleAttrAccess(trim($buffer, '.'))) {

			// Autocomplete for attr access of objects from current scope.
			// We need to separate parts of nested attr access, so that
			// for "obj" we suggest attrs from "obj", but for
			// "obj.attr_a.attr_b" we complete attrs from the final "attr_b".
			$parts = array_reverse(explode('.', $buffer));

			// Fetch the actual variable (always for the first part).
			$name = array_pop($parts);
			if (!$obj = $scopeComp->getVariable($name)) {
				return [];
			}
			$names[] = $name;

			// Now go fetch the attrs inside that variable, if there are any
			// specified in the buffer. E.g. "some_obj.attr_a.att[TAB]" needs
			// to fetch "some_obj", then its attr "attr_a" and get all attrs
			// inside it, so we can suggest "some_obj.attr_a.attr_b"
			$prefix = $name;
			while ($name = array_pop($parts)) {

				if (!$innerObj = $obj->attrGet($name)) {
					break;
				}

				$obj = $innerObj;
				$prefix .= ".$name";
				$names[] = $prefix;

			}

			// The name is now either the rest of the string after last dot,
			// or null (we processed all parts already).
			// If if's not null, we're going to suggest attributes from the
			// object we encountered last.
			if ($name !== null) {

				$underscored = \str_starts_with($name, '_');
				foreach ($obj->dirItems() as $name) {

					// Skip underscored names if not requested explicitly.
					if (!$underscored && Func::is_under_name($name)) {
						continue;
					}

					$addName = "$prefix.$name";
					$names[] = $addName;

				}
			}

		}

		$names = array_filter(
			$names,
			static fn($name) => \str_starts_with($name, $buffer)
		);

		// Typing "some_v[TAB]" if "some_var" exists should also
		// suggest "some_var " with a space, so there are two options for
		// the autocomplete. Why? Otherwise readline would just write
		// "some_var " with a space when the TAB is pressed (it does that
		// automatically). And that's not user friendly if our user would
		// like to access attributes via "some_var." quickly.
		$starting = array_filter(
			$names,
			static fn($n) => str_starts_with($n, $buffer)
		);

		// If there's only one candidate (eg. "debugger"), but it's not yet
		// entered completely ("debugge"), add another candidate with space at
		// the end ("debugger "), so that readline autocomplete stops at the
		// latest common character ("debugger|") instead of resolving into
		// "debugger |" directly (readline adds the space automatically and
		// we don't want that).
		if (count($starting) === 1 && reset($starting) !== $buffer) {
			$names[] = reset($starting) . " ";
		}

		return $names;

	}

}
