<?php

declare(strict_types=1);

namespace Smuuf\Primi\StdLib;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Extensions\Extension;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\ListValue;
use \Smuuf\Primi\Values\NullValue;
use \Smuuf\Primi\Values\FuncValue;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Values\NumberValue;

class ListExtension extends Extension {

	/**
	 * Returns a new copy of the `list`.
	 */
	public static function list_copy(ListValue $list): ListValue {
		return clone $list;
	}

	/**
	 * Returns a new `list` with values of the original `list` reversed.
	 *
	 * ```js
	 * [1, 2, 3].reverse() == [3, 2, 1]
	 * ```
	 */
	public static function list_reverse(ListValue $list): ListValue {
		return new ListValue(\array_reverse($list->value));
	}

	/**
	 * Returns a random item from the `list`.
	 *
	 * ```js
	 * [1, 2, 3].random() // Either 1, 2, or 3.
	 * ```
	 */
	public static function list_random(ListValue $list): AbstractValue {
		return $list->value[\array_rand($list->value)];
	}

	/**
	 * Returns number of occurrences of some value in the `list`.
	 *
	 * ```js
	 * [1, 2, 3, 1].count(1) == 2
	 * [1, 2, 3, 1].count(2) == 1
	 * [1, 2, 3, 1].count(666) == 0
	 *
	 * // NOTE: Lists with same items with different order are different.
	 * [[1, 2], [2, 1]].count([1, 2]) == 1
	 *
	 * // NOTE: Dicts with same items with different order are the same.
	 * [{'a': 1, 'b': 2}, {'b': 2, 'a': 1}].count({'a': 1, 'b': 2}) == 2
	 * ```
	 */
	public static function list_count(
		ListValue $list,
		AbstractValue $needle
	): NumberValue {

		$counter = 0;
		foreach ($list->value as $item) {
			if ($item->isEqualTo($needle)) {
				$counter++;
			}
		}

		return new NumberValue((string) $counter);

	}

	/**
	 * Returns a new `list` with shuffled items.
	 *
	 * ```js
	 * [1, 2].shuffle() // Either [1, 2] or [2, 1]
	 * ```
	 */
	public static function list_shuffle(ListValue $list): ListValue {

		// Do NOT modify the original array (as PHP would do).
		$copy = clone $list;
		\shuffle($copy->value);

		return $copy;

	}

	/**
	 * Returns a new `list` from results of a passed function _(callback)_
	 * applied to each item.
	 *
	 * Callback arguments: `callback(value)`.
	 *
	 * ```js
	 * [-1, 0, 2].map(to_bool) == [true, false, true]
	 * ```
	 *
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

	/**
	 * Returns `true` if the `needle` is present in the `list` at least once.
	 *
	 * ```js
	 * [1, 2, 3, 1].contains(1) == true
	 * [1, 2, 3, 1].contains(666) == false
	 *
	 * // NOTE: Lists with same items with different order are different.
	 * [[1, 2], 'xxx'.contains([1, 2]) == true
	 * [[1, 2], 'xxx'.contains([2, 1]) == false
	 *
	 * // NOTE: Dicts with same items with different order are the same.
	 * [{'b': 2, 'a': 1}, 'xxx'].contains({'a': 1, 'b': 2}) == true
	 * ```
	 */
	public static function list_contains(
		ListValue $list,
		AbstractValue $needle
	): BoolValue {
		return new BoolValue($list->doesContain($needle));
	}

	/**
	 * Returns an item from `list` by its index _(starting at 0)_. Negative
	 * indexes can be used to get items from the end.
	 *
	 * A default is returned in case the index is not found. This default
	 * value can be optionally specified via the `default` parameter _(`null`
	 * by default)_
	 *
	 * ```js
	 * ['a', 'b', 'c'].get(0) == 'a'
	 * ['a', 'b', 'c'].get(1) == 'b'
	 * ['a', 'b', 'c'].get(2) == 'c'
	 * ['a', 'b', 'c'].get(3) == null
	 * ['a', 'b', 'c'].get(3, 'NOT FOUND') == 'NOT FOUND'
	 *
	 * // Using negative index.
	 * ['a', 'b', 'c'].get(-1) == 'c'
	 * ['a', 'b', 'c'].get(-2) == 'b'
	 * ['a', 'b', 'c'].get(-3) == 'a'
	 * ['a', 'b', 'c'].get(-4) == null
	 * ['a', 'b', 'c'].get(-4, 'NOT FOUND') == 'NOT FOUND'
	 * ```
	 */
	public static function list_get(
		ListValue $list,
		NumberValue $index,
		AbstractValue $default = \null
	): AbstractValue {

		// If the index is not found, this will return null.
		$index = $list->protectedIndex((int) $index->value, \false);
		if ($index === \null) {
			return $default ?? new NullValue;
		}

		return $list->value[$index];

	}

	/**
	 * Add (push) an item to the end of the `list`.
	 *
	 * ```js
	 * a_list = ['a', 'b', 'c']
	 * a_list.push({'some_key': 'some_value'})
	 * a_list == ['a', 'b', 'c', {'some_key': 'some_value'}]
	 * ```
	 */
	public static function list_push(
		ListValue $list,
		AbstractValue $value
	): NullValue {
		$list->value[] = $value;
		return new NullValue;
	}

	/**
	 * Prepend an item to the beginning of the `list`.
	 *
	 * ```js
	 * a_list = ['a', 'b', 'c']
	 * a_list.prepend({'some_key': 'some_value'})
	 * a_list == [{'some_key': 'some_value'}, 'a', 'b', 'c']
	 * ```
	 */
	public static function list_prepend(
		ListValue $list,
		AbstractValue $value
	): NullValue {

		// array_unshift() will reindex internal array, which is what we want.
		\array_unshift($list->value, $value);
		return new NullValue;

	}

	/**
	 * Remove (pop) item at specified `index` from the `list` and return it.
	 *
	 * If the `index` is not specified, last item in the `list` will be
	 * removed.  Negative index can be used.
	 *
	 * ```js
	 * a_list = [1, 2, 3, 4, 5]
	 *
	 * a_list.pop() == 5 // a_list == [1, 2, 3, 4], 5 is returned
	 * a_list.pop(1) == 2 // a_list == [1, 3, 4], 2 is returned.
	 * a_list.pop(-3) == 1 // a_list == [3, 4], 1 is returned
	 * ```
	 */
	public static function list_pop(
		ListValue $list,
		?NumberValue $index = \null
	): AbstractValue {

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
