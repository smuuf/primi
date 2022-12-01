<?php

declare(strict_types=1);

namespace Smuuf\Primi\Structures;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Values\DictValue;
use \Smuuf\Primi\Values\TupleValue;
use \Smuuf\Primi\Values\AbstractValue;

/**
 * Container for passing arguments - positional and keyword arguments - to the
 * function-invoking mechanism.
 *
 * NOTE: Instances of this class are meant to be immutable, so self::getEmpty()
 * singleton factory really can always return the same instance of empty
 * CallArgs object.
 *
 * NOTE: Only docblock type-hinting for performance reasons.
 *
 * @internal
 */
class CallArgs {

	use StrictObject;

	private static self $emptySingleton;

	/** @var AbstractValue[] Positional arguments. */
	private $args = [];

	/** @var array<string, AbstractValue> Keyword argument. */
	private $kwargs = [];

	/** True if there are no args and no kwargs specified. */
	private bool $isEmpty = \false;

	/** Total number of args and kwargs combined. */
	private ?int $totalCount = \null;

	/**
	 * @param array<int, AbstractValue> $args
	 * @param array<string, AbstractValue> $kwargs
	 */
	public function __construct(array $args = [], array $kwargs = []) {

		if (!\array_is_list($args)) {
			throw new EngineError(
				"Positional arguments must be specified as a list array");
		}

		$this->args = $args;
		$this->kwargs = $kwargs;

	}

	public static function initEmpty(): void {
		self::$emptySingleton = new self;
	}

	public static function getEmpty(): self {
		return self::$emptySingleton;
	}

	/**
	 * Returns true if there are no args and no kwargs specified. Return false
	 * otherwise.
	 */
	public function isEmpty(): bool {
		return $this->isEmpty
			?? ($this->isEmpty = !($this->args || $this->kwargs));
	}

	public function getTotalCount(): int {
		return $this->totalCount
			?? ($this->totalCount = \count($this->args) + \count($this->kwargs));
	}

	/**
	 * @return AbstractValue[]
	 */
	public function getArgs() {
		return $this->args;
	}

	/**
	 * @return array<string, AbstractValue>
	 */
	public function getKwargs() {
		return $this->kwargs;
	}

	/**
	 * @param int $count Number of expected arguments.
	 * @param int $optional Number of optional arguments (at the end).
	 * @return array<int, AbstractValue|null>
	 */
	public function extractPositional($count, $optional = 0) {

		if ($this->kwargs) {
			$first = \array_key_first($this->kwargs);
			throw new TypeError("Unexpected keyword argument '$first'");
		}

		$argCount = \count($this->args);
		if ($argCount > $count) {
			throw new TypeError("Expected maximum of $count arguments");
		}

		$mandatoryCount = $count - $optional;
		if ($argCount < $mandatoryCount) {
			throw new TypeError("Expected at least $mandatoryCount arguments");
		}

		// Return exactly expected number of arguments. Any non-specified
		// optional arguments will be returned as null.
		return \array_pad($this->args, $count, \null);

	}

	/**
	 * NOTE: Only docblock type-hinting for performance reasons.
	 *
	 * @param array<string> $names
	 * @param array<string> $optional
	 * @return array<string, AbstractValue|null>
	 */
	public function extract($names, $optional = []) {

		// Find names for variadic *args and **kwargs parameters.
		$varArgsName = \false;
		$varKwargsName = \false;
		$varArgs = [];
		$varKwargs = [];
		$final = [];
		$i = 0;

		foreach ($this->args as $value) {

			$name = $names[$i] ?? \null;
			if ($name === \null) {
				throw new TypeError("Too many positional arguments");
			}

			if ($name[0] === '*') {

				// If we encountered variadic kwarg parameter during assigning
				// positional arguments, it is obvious the caller sent us
				// too many positional arguments.
				if ($name[1] === '*') {
					throw new TypeError("Too many positional arguments");
				}

				if (!$varArgsName) {
					$varArgsName = \substr($name, 1);
				}

				$varArgs[] = $value;

			} else {
				$final[$name] = $value;
				$i++;
			}

		}

		// Go through the rest of names to find any potentially specified
		// *args and **kwargs parameters.
		while ($name = $names[$i++] ?? \null) {
			if ($name[0] === '*') {
				if ($name[1] === '*' && !$varKwargsName) {
					$varKwargsName = \substr($name, 2);
				} elseif (!$varArgsName) {
					$varArgsName = \substr($name, 1);
				}
			}
		}

		if ($varArgsName) {
			$final[$varArgsName] = new TupleValue($varArgs);
		}

		// Now let's process keyword arguments.
		foreach ($this->kwargs as $key => $value) {

			// If this kwarg key is not at all present in known definition
			// args, we don't expect this kwarg, so we throw an error.
			if (\in_array($key, $names, \true)) {

				// If this kwarg overwrites already specified final arg
				// (unspecified are false, so isset() works here), throw an
				// error.
				if (!empty($final[$key])) {
					throw new TypeError("Argument '$key' passed multiple times");
				}

				$final[$key] = $value;

			} elseif ($varKwargsName) {

				// If there was an unexpected kwarg, but there is a kwarg
				// collector defined, add this unexpected kwarg to it.
				$varKwargs[$key] = $value;

			} else {
				throw new TypeError("Unexpected keyword argument '$key'");
			}

		}

		if ($varKwargsName) {
			$final[$varKwargsName] = new DictValue(Func::array_to_couples($varKwargs));
		}

		// If there are any "null" values left in the final args dict,
		// some arguments were left out and that is an error.
		foreach ($names as $name) {
			if (
				$name[0] !== '*'
				&& !\array_key_exists($name, $final)
				&& !\in_array($name, $optional, \true)
			) {
				throw new TypeError("Missing required argument '$name'");
			}
		}

		return $final;

	}

	/**
	 * Return new `CallArgs` object with original args and kwargs being
	 * added (positional args) or overwritten (keyword args) by the args
	 * from the "extra" `CallArgs` object passed as the argument.
	 */
	public function withExtra(CallArgs $extra): self {
		return new self(
			[...$this->args, ...$extra->getArgs()],
			\array_merge($this->kwargs, $extra->getKwargs())
		);
	}

}

CallArgs::initEmpty();
