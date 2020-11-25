<?php

declare(strict_types=1);

namespace Smuuf\Primi\Psl;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\ListValue;
use \Smuuf\Primi\Structures\NullValue;
use \Smuuf\Primi\Structures\FuncValue;
use \Smuuf\Primi\Structures\BoolValue;
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

	/**
	 * @injectContext
	 */
	public static function list_map(
		Context $ctx,
		ListValue $list,
		FuncValue $fn
	): ListValue {

		$result = [];
		foreach ($list->value as $k => $v) {
			$result[$k] = $fn->invoke($ctx, [$v]);
		}

		return new ListValue($result);

	}

	public static function list_contains(
		ListValue $list,
		Value $needle
	): BoolValue {
		return new BoolValue($list->doesContain($needle));
	}

	public static function list_get(
		ListValue $list,
		NumberValue $index,
		Value $default = \null
	): Value {

		// If the index is not found, this will return null.
		$index = $list->protectedIndex((int) $index->value, false);
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

			$index = $list->protectedIndex((int) $index->value);
			$popped = $list->value[$index];

			// Remove the item and reindex the list.
			unset($list->value[$index]);
			$list->reindex();

			return $popped;

		}

	}

}
