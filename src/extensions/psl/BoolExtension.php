<?php

namespace Smuuf\Primi\Psl;

use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Extension;

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
		return new BoolValue(!$value->value);
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
		return new BoolValue($a->value && $b->value);
	}

}
