<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use Smuuf\Primi\Stdlib\StaticTypes;

/**
 * Class for representing dynamically created Primi iterables.
 *
 * For example, we want "range()" function (implemented in PHP) to return
 * not only a one-time iterator, but an iterable that allows multiple uses
 * as an iterator. Thus, PHP code handling the "range()" builtin Primi function
 * will return this "iteratorfactory" with a callable acting as a factory
 * function embedded within it.
 *
 * Then, every time someone requests an iterator from this "iteratorfactory",
 * the factory callable will be executed and a brand-new generator is then
 * used as the actual internal iterator which then yields the expected values.
 */
class IteratorFactoryValue extends AbstractBuiltinValue {

	public const TYPE = "iteratorfactory";
	protected string $name;

	/**
	 * @param callable(mixed ...$args): \Iterator<AbstractValue> $factory
	 */
	public function __construct(callable $factory, string $name) {
		$this->value = $factory;
		$this->name = $name;
	}

	public function getType(): TypeValue {
		return StaticTypes::getIteratorFactoryType();
	}

	public function getStringRepr(): string {
		return "<iteratorfactory '{$this->name}'>";
	}

	/**
	 * @return \Iterator<AbstractValue>
	 */
	public function getIterator(): \Iterator {
		yield from ($this->value)();
	}

}
