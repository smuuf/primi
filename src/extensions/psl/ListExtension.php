<?php

declare(strict_types=1);

namespace Smuuf\Primi\Psl;

use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\ListValue;
use \Smuuf\Primi\Structures\NullValue;
use \Smuuf\Primi\Structures\FuncValue;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Structures\DictValue;
use \Smuuf\Primi\Structures\StringValue;
use \Smuuf\Primi\Structures\NumberValue;

class ListExtension extends Extension {

	public static function list_copy(ListValue $list): ListValue {
		return clone $list;
	}

	public static function list_reverse(ListValue $list): Value {
		return new ListValue(\array_reverse($list->value));
	}

	public static function list_random(ListValue $list): Value {
		return $list->value[\array_rand($list->value)];
	}

	public static function list_count(ListValue $list, Value $needle): NumberValue {

		// We must convert Primi values back to PHP values for the
		// array_count_values function to work.
		$phpValues = \array_map(function($item) {
		        return $item->value;
		}, $list->value);

		$valuesCount = \array_count_values($phpValues);
		$count = $valuesCount[$needle->value] ?? 0;

		return new NumberValue((string) $count);

	}

	public static function list_shuffle(ListValue $list): ListValue {

		// Do NOT modify the original array (as PHP would do).
		$copy = clone $list;
		\shuffle($copy->value);

		return $copy;

	}

	public static function list_map(ListValue $list, FuncValue $fn): ListValue {

		$result = [];
		foreach ($list->value as $k => $v) {
			$result[$k] = $fn->invoke([$v]);
		}

		return new ListValue($result);

	}

	public static function list_contains(
		ListValue $list,
		Value $needle
	): BoolValue {

		// Allow only some value types.
		Func::allow_argument_types(
			1, $needle,
			StringValue::class, NumberValue::class, ListValue::class,
			DictValue::class, BoolValue::class, NullValue::class
		);

		// Let's see if the needle object is in list value (which is an array of
		// Primi value objects). Non-strict search allows to match dictionaries
		// with the same key-values but in different order (needs testing).
		return new BoolValue(\in_array($needle, $list->value));

	}

	public static function list_get(
		ListValue $list,
		NumberValue $index,
		Value $default = \null
	): Value {

		// If the index is not found, this will return null.
		$index = $list->protectedIndex($index->value, false);
		if ($index === null) {
			return $default ?? new NullValue;
		}

		return $list->value[$index];

	}

	public static function list_push(ListValue $list, Value $value): NullValue {
		$list->value[] = $value;
		return new NullValue;
	}

	public static function list_prepend(ListValue $list, Value $value): NullValue {
		// array_unshift() will reindex internal array, which is what we want.
		array_unshift($list->value, $value);
		return new NullValue;
	}

	public static function list_pop(ListValue $list, ?NumberValue $index = null): Value {

		if ($index === null) {

			// If index was not specified, pop and return the last item.
			return \array_pop($list->value);

		} else {

			// If an index was specified, pop and return item with that index.
			$index = $list->protectedIndex($index->value);
			$popped = $list->value[$index];

			// Remove the item and reindex the list.
			unset($list->value[$index]);
			$list->reindex();

			return $popped;

		}

	}

}
