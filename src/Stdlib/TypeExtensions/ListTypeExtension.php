<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\IndexError;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Values\ListValue;
use \Smuuf\Primi\Values\NullValue;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Extensions\TypeExtension;
use \Smuuf\Primi\Helpers\Indices;
use \Smuuf\Primi\Structures\CallArgs;
use \Smuuf\Primi\Values\TypeValue;

class ListTypeExtension extends TypeExtension {

	/**
	 * @primi.function(no-stack)
	 */
	public static function __new__(
		TypeValue $_,
		?AbstractValue $value = \null
	): ListValue {

		// No argument - create empty list.
		if ($value === \null) {
			return new ListValue([]);
		}

		$iter = $value->getIterator();
		if ($iter === \null) {
			throw new RuntimeError('list() argument must be iterable');
		}

		return new ListValue(\iterator_to_array($iter));
	}

	/**
	 * Returns a new copy of the `list`.
	 * @primi.function
	 */
	public static function copy(ListValue $list): ListValue {
		return clone $list;
	}

	/**
	 * Returns a new `list` with values of the original `list` reversed.
	 *
	 * ```js
	 * [1, 2, 3].reverse() == [3, 2, 1]
	 * ```
	 * @primi.function
	 */
	public static function reverse(ListValue $list): ListValue {
		return new ListValue(\array_reverse($list->value));
	}

	/**
	 * Returns a random item from the `list`.
	 *
	 * ```js
	 * [1, 2, 3].random() // Either 1, 2, or 3.
	 * ```
	 * @primi.function
	 */
	public static function random(ListValue $list): AbstractValue {
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
	 * @primi.function
	 */
	public static function count(
		ListValue $list,
		AbstractValue $needle
	): NumberValue {

		$counter = 0;
		foreach ($list->value as $item) {
			if ($item->isEqualTo($needle)) {
				$counter++;
			}
		}

		return Interned::number((string) $counter);

	}

	/**
	 * Returns a new `list` with shuffled items.
	 *
	 * ```js
	 * [1, 2].shuffle() // Either [1, 2] or [2, 1]
	 * ```
	 * @primi.function
	 */
	public static function shuffle(ListValue $list): ListValue {

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
	 * @primi.function(inject-context)
	 */
	public static function map(
		Context $ctx,
		ListValue $list,
		AbstractValue $callable
	): ListValue {

		$result = [];
		foreach ($list->value as $k => $v) {
			$result[$k] = $callable->invoke($ctx, new CallArgs([$v]));
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
	 * [[1, 2], 'xxx'].contains([1, 2]) == true
	 * [[1, 2], 'xxx'].contains([2, 1]) == false
	 *
	 * // NOTE: Dicts with same items with different order are the same.
	 * [{'b': 2, 'a': 1}, 'xxx'].contains({'a': 1, 'b': 2}) == true
	 * ```
	 * @primi.function
	 */
	public static function contains(
		ListValue $list,
		AbstractValue $needle
	): BoolValue {
		return Interned::bool($list->doesContain($needle));
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
	 * @primi.function
	 */
	public static function get(
		ListValue $list,
		NumberValue $index,
		AbstractValue $default = \null
	): AbstractValue {

		// If the index is not found, this will return null.
		$actualIndex = Indices::resolveNegativeIndex(
			(int) $index->value,
			\count($list->value) - 1
		);

		if ($actualIndex === \null) {
			return $default ?? Interned::null();
		}

		return $list->value[$actualIndex];

	}

	/**
	 * Add (push) an item to the end of the `list`.
	 *
	 * ```js
	 * a_list = ['a', 'b', 'c']
	 * a_list.push({'some_key': 'some_value'})
	 * a_list == ['a', 'b', 'c', {'some_key': 'some_value'}]
	 * ```
	 * @primi.function
	 */
	public static function push(
		ListValue $list,
		AbstractValue $value
	): NullValue {
		$list->value[] = $value;
		return Interned::null();
	}

	/**
	 * Prepend an item to the beginning of the `list`.
	 *
	 * ```js
	 * a_list = ['a', 'b', 'c']
	 * a_list.prepend({'some_key': 'some_value'})
	 * a_list == [{'some_key': 'some_value'}, 'a', 'b', 'c']
	 * ```
	 * @primi.function
	 */
	public static function prepend(
		ListValue $list,
		AbstractValue $value
	): NullValue {

		// array_unshift() will reindex internal array, which is what we want.
		\array_unshift($list->value, $value);
		return Interned::null();

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
	 * @primi.function
	 */
	public static function pop(
		ListValue $list,
		?NumberValue $index = \null
	): AbstractValue {

		if ($index === \null) {

			if (!$list->value) {
				throw new IndexError(-1);
			}

			// If index was not specified, pop and return the last item.
			return \array_pop($list->value);

		} else {

			// If the index is not found, this will throw IndexError.
			$actualIndex = Indices::resolveIndexOrError(
				(int) $index->value,
				$list->value
			);

			$popped = $list->value[$actualIndex];

			// Take the part of the array before the item we've just popped
			// and after it - and merge it using the spread operator, which
			// will reindex the array, which we probably want.
			$list->value = [
				...\array_slice($list->value, 0, $actualIndex),
				...\array_slice($list->value, $actualIndex + 1)
			];

			return $popped;

		}

	}

}
