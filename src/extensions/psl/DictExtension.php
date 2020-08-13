<?php

namespace Smuuf\Primi\Psl;

use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\DictValue;
use \Smuuf\Primi\Structures\NullValue;
use \Smuuf\Primi\Structures\FuncValue;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Structures\StringValue;
use \Smuuf\Primi\Structures\NumberValue;

use function \Smuuf\Primi\Helpers\allow_argument_types as primifn_allow_argument_types;

class DictExtension extends Extension {

	public static function dict_copy(DictValue $arr): DictValue {
		return clone $arr;
	}

	public static function dict_reverse(DictValue $arr): Value {
		return new DictValue(\array_reverse($arr->value));
	}

	public static function dict_random(DictValue $arr): Value {
		return $arr->value[\array_rand($arr->value)];
	}

	public static function dict_shuffle(DictValue $arr): DictValue {

		// Do NOT modify the original array argument (as PHP would do).
		$copy = clone $arr;
		\shuffle($copy->value);

		return $copy;

	}

	public static function dict_map(DictValue $arr, FuncValue $fn): DictValue {

		$result = [];
		foreach ($arr->value as $k => $v) {
			$result[$k] = $fn->invoke([$v]);
		}

		return new DictValue($result);

	}

	public static function dict_contains(DictValue $arr, Value $needle): BoolValue {

		// Allow only some value types.
		primifn_allow_argument_types(1, $needle, StringValue::class, NumberValue::class);

		// Let's search the $needle object in $arr's value (array of objects).
		return new BoolValue(\array_search($needle, $arr->value) !== \false);

	}

	public static function dict_has(DictValue $arr, Value $key): BoolValue {

		// Allow only some value types.
		primifn_allow_argument_types(1, $key, StringValue::class, NumberValue::class);

		// Return true if the key exists in this array.
		return new BoolValue(isset($arr->value[$key->value]));

	}

	public static function dict_get(DictValue $arr, Value $key, Value $default = \null): Value {

		// Allow only some value types.
		primifn_allow_argument_types(1, $key, StringValue::class, NumberValue::class);
		return $arr->value[$key->value] ?? $default ?? new NullValue;

	}

	public static function dict_number_of(DictValue $arr, Value $needle): NumberValue {

		// Allow only some value types.
		primifn_allow_argument_types(1, $needle, StringValue::class, NumberValue::class);

		// We must convert Primi values back to PHP values for the
		// array_count_values function to work.
		$phpValues = \array_map(function($item) {
			return $item->value;
		}, $arr->value);

		$valuesCount = \array_count_values($phpValues);
		$count = $valuesCount[$needle->value] ?? 0;

		return new NumberValue((string) $count);

	}

	public static function dict_push(DictValue $arr, Value $value): NullValue {
		$arr->value[] = $value;
		return new NullValue;
	}

	public static function dict_pop(DictValue $arr): Value {
		return \array_pop($arr->value);
	}

}
