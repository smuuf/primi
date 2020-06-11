<?php

namespace Smuuf\Primi\Psl;

use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Structures\NumberValue;

class NumberExtension extends Extension {

	/**
	 * Returns number `n` rounded to specified `precision`. If the \
	 * precision is not specified, a default `prevision` of zero is used.
	 */
	public static function number_round(NumberValue $n, NumberValue $precision = \null): NumberValue {
		return new NumberValue(\round($n->value, $precision ? $precision->value : 0));
	}

	/** Returns the absolute value of number `n`. */
	public static function number_abs(NumberValue $n): NumberValue {
		return new NumberValue(abs($n->value));
	}

	/** Returns number `n` rounded up. */
	public static function number_ceil(NumberValue $n): NumberValue {
		return new NumberValue(\ceil($n->value));
	}

	/** Returns number `n` rounded down. */
	public static function number_floor(NumberValue $n): NumberValue {
		return new NumberValue(\floor($n->value));
	}

	/** Returns the square root of a number `n`. */
	public static function number_sqrt(NumberValue $n): NumberValue {
		return new NumberValue(\sqrt($n->value));
	}

	/** Returns number `n` squared to the power of `power` */
	public static function number_pow(NumberValue $n, NumberValue $power = \null): NumberValue {
		return new NumberValue($n->value ** ($power === \null ? 2 : $power->value));
	}

	/** Returns the sine of number `n` specified in radians. */
	public static function number_sin(NumberValue $n): NumberValue {
		return new NumberValue(\sin($n->value));
	}

	/** Returns the cosine of number `n` specified in radians. */
	public static function number_cos(NumberValue $n): NumberValue {
		return new NumberValue(\cos($n->value));
	}

	/** Returns the tangent of number `n` specified in radians. */
	public static function number_tan(NumberValue $n): NumberValue {
		return new NumberValue(\tan($n->value));
	}

	/** Returns the arc tangent of number `n` specified in radians. */
	public static function number_atan(NumberValue $n): NumberValue {
		return new NumberValue(\atan($n->value));
	}

}
