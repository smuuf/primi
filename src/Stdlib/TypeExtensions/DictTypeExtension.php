<?php

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\UnhashableTypeException;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\DictValue;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Values\ListValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Extensions\TypeExtension;
use \Smuuf\Primi\Structures\CallArgs;
use \Smuuf\Primi\Values\TypeValue;

class DictTypeExtension extends TypeExtension {

	/**
	 * @primi.function(no-stack)
	 */
	public static function __new__(
		TypeValue $_,
		?AbstractValue $value = \null
	): DictValue {

		// Default value for a new number is 0.
		if ($value === \null) {
			return new DictValue([]);
		}

		$iter = $value->getIterator();
		if ($iter === \null) {
			throw new RuntimeError('dict() argument must be iterable');
		}

		return new DictValue(Func::iterator_as_tuples($iter));

	}

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
	 *
	 * @primi.function
	 */
	public static function get(
		DictValue $dict,
		AbstractValue $key,
		?AbstractValue $default = \null
	): AbstractValue {

		try {
			return $dict->value[$key] ?? $default ?? Interned::null();
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
	 *
	 * @primi.function
	 */
	public static function has_value(
		DictValue $dict,
		AbstractValue $needle
	): BoolValue {
		return Interned::bool($dict->value->findValue($needle) !== \null);
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
	 *
	 * @primi.function
	 */
	public static function has_key(
		DictValue $dict,
		AbstractValue $key
	): BoolValue {

		try {
			return Interned::bool($dict->doesContain($key));
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
	 *
	 * @primi.function
	 */
	public static function values(DictValue $dict): ListValue {

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
	 *
	 * @primi.function
	 */
	public static function keys(DictValue $dict): ListValue {

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
	 *
	 * @primi.function
	 */
	public static function copy(DictValue $dict): DictValue {
		return clone $dict;
	}

	/**
	 * Returns a new `dict` with original `dict`'s items in reversed order.
	 *
	 * ```js
	 * {'a': 1, 100: 'yes'}.reverse() == {100: 'yes', 'a': 1}
	 * ```
	 * @primi.function
	 */
	public static function reverse(DictValue $dict): AbstractValue {
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
	 * @primi.function(inject-context)
	 */
	public static function map(
		Context $ctx,
		DictValue $dict,
		AbstractValue $callable
	): DictValue {

		$result = [];
		foreach ($dict->value as $k => $v) {
			$result[] = [
				$k,
				$callable->invoke(
					$ctx,
					new CallArgs([$v, $k])
				)
			];
		}

		return new DictValue($result);

	}

}
