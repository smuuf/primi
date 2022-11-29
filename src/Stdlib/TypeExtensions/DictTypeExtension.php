<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Extensions\PrimiFunc;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Ex\UnhashableTypeException;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\DictValue;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Values\ListValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Extensions\TypeExtension;
use \Smuuf\Primi\Stdlib\BuiltinTypes;
use \Smuuf\Primi\Structures\CallArgs;
use \Smuuf\Primi\Values\TupleValue;
use \Smuuf\Primi\Values\TypeValue;

class DictTypeExtension extends TypeExtension {

	#[PrimiFunc]
	public static function __new__(
		TypeValue $type,
		?AbstractValue $value = \null
	): DictValue {

		if ($type !== BuiltinTypes::getDictType()) {
			throw new TypeError("Passed invalid type object");
		}

		// Default value for a new number is 0.
		if ($value === \null) {
			return new DictValue([]);
		}

		$iter = $value->getIterator();
		if ($iter === \null) {
			throw new TypeError('dict() argument must be iterable');
		}

		try {
			return new DictValue(Func::mapping_to_couples($value));
		} catch (UnhashableTypeException $e) {
			throw new TypeError(\sprintf(
				"Cannot create dict with key containing unhashable type '%s'",
				$e->getType()
			));
		}

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
	 */
	#[PrimiFunc]
	public static function get(
		DictValue $dict,
		AbstractValue $key,
		?AbstractValue $default = \null
	): AbstractValue {

		try {
			return $dict->value->get($key) ?? $default ?? Interned::null();
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
	#[PrimiFunc]
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
	 */
	#[PrimiFunc]
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
	 * Returns a new `list` of `tuples` of **key and value pairs** from this
	 * `dict`.
	 *
	 * ```js
	 * {'a': 1, 100: 'yes'}.items() == [('a', 1), (100: 'yes')]
	 * ```
	 */
	#[PrimiFunc]
	public static function items(DictValue $dict): ListValue {

		$list = [];
		foreach ($dict->value->getItemsIterator() as $arrayTuple) {
			$list[] = new TupleValue($arrayTuple);
		}

		return new ListValue($list);

	}

	/**
	 * Returns a new `list` containing **values** from this `dict`.
	 *
	 * ```js
	 * {'a': 1, 100: 'yes'}.values() == [1, 'yes']
	 * ```
	 */
	#[PrimiFunc]
	public static function values(DictValue $dict): ListValue {
		return new ListValue(
			\iterator_to_array($dict->value->getValuesIterator())
		);
	}

	/**
	 * Returns a new `list` containing **keys** from this `dict`.
	 *
	 * ```js
	 * {'a': 1, 100: 'yes'}.values() == [1, 'yes']
	 * ```
	 */
	#[PrimiFunc]
	public static function keys(DictValue $dict): ListValue {
		return new ListValue(
			\iterator_to_array($dict->value->getKeysIterator())
		);
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
	#[PrimiFunc]
	public static function copy(DictValue $dict): DictValue {
		return clone $dict;
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
	 */
	#[PrimiFunc(toStack: \true, callConv: PrimiFunc::CONV_CALLARGS)]
	public static function map(
		CallArgs $args,
		Context $ctx
	): DictValue {

		[$self, $callable] = $args->extractPositional(2);
		Func::allow_argument_types(1, $self, BuiltinTypes::getDictType());

		$result = [];
		foreach ($self->value->getItemsIterator() as [$k, $v]) {
			$result[] = [
				$k,
				$callable->invoke($ctx, new CallArgs([$v, $k]))
			];
		}

		return new DictValue($result);

	}

}
