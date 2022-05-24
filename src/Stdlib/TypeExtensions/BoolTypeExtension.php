<?php

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Values\TypeValue;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Extensions\TypeExtension;
use \Smuuf\Primi\Stdlib\StaticTypes;

class BoolTypeExtension extends TypeExtension {

	/**
	 * @primi.func(no-stack)
	 */
	public static function __new__(
		TypeValue $type,
		?AbstractValue $value = \null
	): BoolValue {

		if ($type !== StaticTypes::getBoolType()) {
			throw new TypeError("Passed invalid type object");
		}

		return $value === \null
			? Interned::bool(\false)
			: Interned::bool($value->isTruthy());

	}

	/**
	 * Returns a negation (logical `NOT`) of a single boolean value.
	 *
	 * ```js
	 * false.not() == true
	 * true.not() == false
	 * bool.not(false) == true
	 * bool.not(true) == false
	 * ```
	 *
	 * @primi.func(no-stack)
	 */
	public static function not(BoolValue $value): BoolValue {
		return Interned::bool(!$value->value);
	}

	/**
	 * Returns result of logical `AND` between two boolean values.
	 *
	 * ```js
	 * true.and(false) == false
	 * true.and(true) == true
	 * bool_and(true, false) == false
	 * ```
	 *
	 * @primi.func(no-stack)
	 */
	public static function and(BoolValue $a, BoolValue $b): BoolValue {
		return Interned::bool($a->value && $b->value);
	}

	/**
	 * Returns an `OR` of two boolean values.
	 *
	 * ```js
	 * true.or(true) == true
	 * true.or(false) == true
	 * false.or(true) == true
	 * false.or(false) == false
	 *
	 * bool_or(true, true) == true
	 * bool_or(true, false) == true
	 * bool_or(false, true) == true
	 * bool_or(false, false) == false
	 * ```
	 *
	 * @primi.func(no-stack)
	 */
	public static function or(BoolValue $a, BoolValue $b): BoolValue {
		return Interned::bool($a->value || $b->value);
	}

	/**
	 * Returns an exclusive `OR` (`XOR`) of two boolean values.
	 *
	 * ```js
	 * true.xor(true) == false
	 * true.xor(false) == true
	 * false.xor(true) == true
	 * false.xor(false) == false
	 *
	 * bool_xor(true, true) == false
	 * bool_xor(true, false) == true
	 * bool_xor(false, true) == true
	 * bool_xor(false, false) == false
	 * ```
	 *
	 * @primi.func(no-stack)
	 */
	public static function xor(BoolValue $a, BoolValue $b): BoolValue {
		return Interned::bool($a->value xor $b->value);
	}

}
