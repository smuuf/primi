<?php

namespace Smuuf\Primi\Stdlib\Extensions;

use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Extensions\Extension;

class BoolExtension extends Extension {

	/**
	 * Returns a negation (logical `NOT`) of a single boolean value.
	 *
	 * ```js
	 * false.not() == true
	 * bool_not(true) == false
	 * ```
	 */
	public static function bool_not(BoolValue $value): BoolValue {
		return BoolValue::build(!$value->value);
	}

	/**
	 * Returns result of logical `AND` between two boolean values.
	 *
	 * ```js
	 * true.and(false) == false
	 * true.and(true) == true
	 * bool_and(true, false) == false
	 * ```
	 */
	public static function bool_and(BoolValue $a, BoolValue $b): BoolValue {
		return BoolValue::build($a->value && $b->value);
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
	 */
	public static function bool_or(BoolValue $a, BoolValue $b): BoolValue {
		return BoolValue::build($a->value || $b->value);
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
	 */
	public static function bool_xor(BoolValue $a, BoolValue $b): BoolValue {
		return BoolValue::build($a->value xor $b->value);
	}

}
