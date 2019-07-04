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

	public function getInternalValue() {
		return $this->value;
	}

	abstract public function getStringValue(): string;

}
