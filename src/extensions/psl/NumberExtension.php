<?php

declare(strict_types=1);

namespace Smuuf\Primi\Psl;

use ErrorException;
use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Structures\NumberValue;

class NumberExtension extends Extension {

	/**
	 * Returns number `n` rounded to specified `precision`. If the \
	 * precision is not specified, a default `prevision` of zero is used.
	 */
	public static function number_round(NumberValue $n, NumberValue $precision = \null): NumberValue {
		return new NumberValue((string) \round(
			$n->value,
			$precision ? (int) $precision->value : 0
		));
	}

	/** Returns the absolute value of number `n`. */
	public static function number_abs(NumberValue $n): NumberValue {
		return new NumberValue((string) \abs($n->value));
	}

	/** Returns number `n` rounded up. */
	public static function number_ceil(NumberValue $n): NumberValue {
		return new NumberValue((string) \ceil($n->value));
	}

	/** Returns number `n` rounded down. */
	public static function number_floor(NumberValue $n): NumberValue {
		return new NumberValue((string) \floor($n->value));
	}

	/** Returns the square root of a number `n`. */
	public static function number_sqrt(NumberValue $n): NumberValue {
		return new NumberValue((string) \bcsqrt(
			$n->value,
			NumberValue::PRECISION
		));
	}

	/** Returns number `n` squared to the power of `power` */
	public static function number_pow(NumberValue $n, NumberValue $power = \null): NumberValue {
		return $n->doPower(
			$power === \null
				? new NumberValue('2')
				: $power
		);
	}

	/** Returns the sine of number `n` specified in radians. */
	public static function number_sin(NumberValue $n): NumberValue {
		return new NumberValue((string) \sin((float) $n->value));
	}

	/** Returns the cosine of number `n` specified in radians. */
	public static function number_cos(NumberValue $n): NumberValue {
		return new NumberValue((string) \cos((float) $n->value));
	}

	/** Returns the tangent of number `n` specified in radians. */
	public static function number_tan(NumberValue $n): NumberValue {
		return new NumberValue((string) \tan((float) $n->value));
	}

	/** Returns the arc tangent of number `n` specified in radians. */
	public static function number_atan(NumberValue $n): NumberValue {
		return new NumberValue((string) \atan((float) $n->value));
	}

}
