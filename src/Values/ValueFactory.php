<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Structures\FnContainer;

abstract class ValueFactory {

	public static function buildAutomatic($value) {

		switch (\true) {
			case $value === \null:
				return new NullValue;
			case \is_bool($value):
				return new BoolValue($value);
			case \is_numeric($value):
				return new NumberValue(Func::scientific_to_decimal((string) $value));
			case \is_callable($value);
				// Must be before "is_array" case, because some "arrays"
				// can be in reality "callables".
				return new FuncValue(FnContainer::buildFromClosure($value));
			case \is_array($value):
				$inner = \array_map([self::class, 'buildAutomatic'], $value);
				if (Func::is_array_dict($value)) {
					return new DictValue(Func::php_array_to_dict_pairs($inner));
				} else {
					return new ListValue($inner);
				}
			case $value instanceof AbstractValue:
				return $value;
			default:
				return new StringValue($value);
		}

	}

}
