<?php

namespace Smuuf\Primi\Psl;

use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Ex\UnhashableTypeException;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\DictValue;
use \Smuuf\Primi\Structures\NullValue;
use \Smuuf\Primi\Structures\FuncValue;
use \Smuuf\Primi\Structures\BoolValue;
use Smuuf\Primi\Structures\ListValue;

class DictExtension extends Extension {

	/**
	 * Returns value stored under `key`, if it in dict, otherwise returns the \
	 * value of the `default` argument, which is `null` by default, but can \
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
		Value $key,
		?Value $default = \null
	): Value {

		try {
			return $dict->value[$key] ?? $default ?? new NullValue;
		} catch (UnhashableTypeException $e) {
			throw new TypeError($e->getMessage());
		}

	}

	/**
	 * Return `true` if the value exists in dict. Return `false` otherwise.
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
		Value $needle
	): BoolValue {
		return new BoolValue($dict->value->findValue($needle) !== null);
	}

	/**
	 * Return `true` if the key exists in dict. Return `false` otherwise.
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
		Value $key
	): BoolValue {

		try {
			return new BoolValue($dict->doesContain($key));
		} catch (UnhashableTypeException $e) {
			throw new TypeError($e->getMessage());
		}

	}

	public static function dict_values(DictValue $dict): ListValue {

		$list = [];
		foreach ($dict->getIterator() as $_ => $value) {
			$list[] = $value;
		}

		return new ListValue($list);

	}

	public static function dict_keys(DictValue $dict): ListValue {

		$list = [];
		foreach ($dict->getIterator() as $key => $_) {
			$list[] = $key;
		}

		return new ListValue($list);

	}

	public static function dict_copy(DictValue $dict): DictValue {
		return clone $dict;
	}

	public static function dict_reverse(DictValue $dict): Value {
		return new DictValue(Func::iterator_as_tuples(
			$dict->value->getReverseIterator()
		));
	}

	public static function dict_map(DictValue $dict, FuncValue $fn): DictValue {

		$result = [];
		foreach ($dict->value as $k => $v) {
			$result[] = [$k, $fn->invoke([$v, $k])];
		}

		return new DictValue($result);

	}

}
