<?php

namespace Smuuf\Primi\StdLib;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Extensions\Extension;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Ex\UnhashableTypeException;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\DictValue;
use \Smuuf\Primi\Values\NullValue;
use \Smuuf\Primi\Values\FuncValue;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Values\ListValue;

class DictExtension extends Extension {

	/**
	 * Returns value stored under `key`, if it in dict, otherwise returns the
	 * value of the `default` argument, which is `null` by default, but can
	 * optionally be specified.
	 *
	 * ```js
	 * d = {'a': 1, 100: 'yes'}
	 * d.get('a') == 1
	 * d.get(100) == 'yes'
	 * d.get('100') == null
	 * d.get('100', ['one', 'hundred']) == ['one', 'hundred']
	 * ```
	 */
	public static function dict_get(
		DictValue $dict,
		AbstractValue $key,
		?AbstractValue $default = \null
	): AbstractValue {

		try {
			return $dict->value[$key] ?? $default ?? NullValue::build();
		} catch (UnhashableTypeException $e) {
			throw new TypeError($e->getMessage());
		}

	}

	/**
	 * Returns `true` if the value exists in dict. Return `false` otherwise.
	 *
	 * ```js
	 * d = {'a': 1, 100: 'yes'}
	 * d.has_value(1) == true
	 * d.has_value('yes') == true
	 * d.has_value(100) == false
	 * d.has_value(false) == false
	 * ```
	 */
	public static function dict_has_value(
		DictValue $dict,
		AbstractValue $needle
	): BoolValue {
		return BoolValue::build($dict->value->findValue($needle) !== \null);
	}

	/**
	 * Returns `true` if the key exists in dict. Return `false` otherwise.
	 *
	 * ```js
	 * d = {'a': 1, 100: 'yes'}
	 * d.has_key('a') == true
	 * d.has_key(100) == true
	 * d.has_key('100') == false
	 * d.has_key('yes') == false
	 * ```
	 */
	public static function dict_has_key(
		DictValue $dict,
		AbstractValue $key
	): BoolValue {

		try {
			return BoolValue::build($dict->doesContain($key));
		} catch (UnhashableTypeException $e) {
			throw new TypeError($e->getMessage());
		}

	}

	/**
	 * Returns a new `list` containing **values** from this `dict`.
	 *
	 * ```js
	 * {'a': 1, 100: 'yes'}.values() == [1, 'yes']
	 * ```
	 */
	public static function dict_values(DictValue $dict): ListValue {

		$list = [];
		foreach ($dict->getIterator() as $_ => $value) {
			$list[] = $value;
		}

		return new ListValue($list);

	}

	/**
	 * Returns a new `list` containing **keys** from this `dict`.
	 *
	 * ```js
	 * {'a': 1, 100: 'yes'}.values() == [1, 'yes']
	 * ```
	 */
	public static function dict_keys(DictValue $dict): ListValue {

		$list = [];
		foreach ($dict->getIterator() as $key => $_) {
			$list[] = $key;
		}

		return new ListValue($list);

	}

	/**
	 * Returns a new shallow copy of this dict.
	 *
	 * ```js
	 * a_dict = {'a': 1, 100: 'yes'}
	 * b_dict = a_dict.copy()
	 * b_dict[100] = 'nope'
	 *
	 * a_dict == {'a': 1, 100: 'yes'}
	 * b_dict == {'a': 1, 100: 'nope'}
	 * ```
	 */
	public static function dict_copy(DictValue $dict): DictValue {
		return clone $dict;
	}

	/**
	 * Returns a new `dict` with original `dict`'s items in reversed order.
	 *
	 * ```js
	 * {'a': 1, 100: 'yes'}.reverse() == {100: 'yes', 'a': 1}
	 * ```
	 */

	public static function dict_reverse(DictValue $dict): AbstractValue {
		return new DictValue(Func::iterator_as_tuples(
			$dict->value->getReverseIterator()
		));
	}

	/**
	 * Returns a new dict with same keys but values returned by a passed
	 * function _(callback)_ applied to each item.
	 *
	 * Callback arguments: `callback(value, key)`.
	 *
	 * ```js
	 * a_dict = {'key_a': 'val_a', 'key_b': 'val_b'}
	 * fn = (v, k) => { return k + "|" + v; }
	 * a_dict.map(fn) == {"key_a": "key_a|val_a", "key_b": "key_b|val_b"}
	 * ```
	 *
	 * @injectContext
	 */
	public static function dict_map(
		Context $ctx,
		DictValue $dict,
		FuncValue $fn
	): DictValue {

		$result = [];
		foreach ($dict->value as $k => $v) {
			$result[] = [$k, $fn->invoke($ctx, [$v, $k])];
		}

		return new DictValue($result);

	}

}
