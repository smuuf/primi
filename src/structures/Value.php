<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Structures\ValueFriends;

abstract class Value extends ValueFriends {

	const TYPE = "__no_type__";

	public static function buildAutomatic($value) {

		switch (\true) {
			case $value === \null:
				return new NullValue;
			case \is_bool($value):
				return new BoolValue($value);
			case \is_callable($value);
				// Must be before "is_array" case, because some "arrays"
				// can be in reality "callables".
				return new FuncValue(FnContainer::buildFromClosure($value));
			case \is_array($value):
				$inner = \array_map([self::class, 'buildAutomatic'], $value);
				return new ArrayValue($inner);
			case NumberValue::isNumeric($value):
				// Must be after "is_array" case.
				return new NumberValue($value);
			default:
				return new StringValue($value);
		}

	}

	/**
	 * Returns the core PHP value of this Primi value object.
	 */
	final public function getInternalValue() {
		return $this->value;
	}

	/**
	 * Returns a string representation of internal value.
	 */
	public function getStringValue(): string {
		return $this->getStringRepr();
	}

	/**
	 * Returns an unambiguous string representation of internal value.
	 *
	 * If possible, is should be in such form that it the result of this
	 * method can be used as Primi source code to recreate that value.
	 */
	abstract public function getStringRepr(): string;

	/**
	 * All values must be able to tell if they're truthy or falsey.
	 * All values are truthy unless they tell otherwise.
	 */
	public function isTruthy(): bool {
		return true;
	}

}
