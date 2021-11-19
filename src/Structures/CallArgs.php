<?php

declare(strict_types=1);

namespace Smuuf\Primi\Structures;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Interned;

/**
 * Container for passing arguments - positional and keyword arguments - to the
 * function-invoking mechanism.
 *
 * NOTE: Instances of this class are meant to be immutable, so self::getEmpty()
 * singleton factory really can always return the same instance of empty
 * CallArgs object.
 *
 * @internal
 */
class CallArgs {

	use StrictObject;

	private static self $emptySingleton;

	/** @var AbstractValue[] Positional arguments. */
	private $args = [];

	/** @var AbstractValue[] Keyword argument. */
	private $kwargs = [];

	/** True if there are no args and no kwargs specified. */
	private bool $isEmpty = \false;

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

	/**
	 * @return AbstractValue[]
	 */
	public function getArgs(): array {
		return $this->args;
	}

	/**
	 * @return AbstractValue[]
	 */
	public function getKwargs(): array {
		return $this->kwargs;
	}

	/**
	 * Returns Primi object stored as positional argument. If not found,
	 * `EngineError` is thrown.
	 */
	public function getArg(int $index): AbstractValue {

		if (isset($this->args[$index])) {
			return$this->args[$index];
		}

		throw new EngineError("Positional argument $index not found");

	}

	/**
	 * Returns Primi object stored as positional argument. If not found,
	 * `EngineError` is thrown.
	 */
	public function getKwarg(string $name): AbstractValue {

		if (isset($this->kwargs[$name])) {
			return$this->kwargs[$name];
		}

		throw new EngineError("Keyword argument '$name' not found");

	}

	/**
	 * Returns Primi object stored as positional argument. If not found, Primi
	 * null object is returned.
	 */
	public function safeGetArg(
		int $index,
		?AbstractValue $default = null
	): AbstractValue {

		return $this->args[$index]
			?? $default
			?? Interned::null();

	}

	/**
	 * Returns Primi object stored as keyword argument. If not found, Primi
	 * null object is returned.
	 */
	public function safeGetKwarg(
		string $name,
		?AbstractValue $default = null
	): AbstractValue {

		return $this->kwargs[$name]
			?? $default
			?? Interned::null();

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
