<?php

declare(strict_types=1);

namespace Smuuf\Primi\StdLib;

use \Smuuf\Primi\Extensions\Extension;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Values\NumberValue;

class NumberExtension extends Extension {

	/**
	 * Returns number `n` rounded to specified `precision`. If the
	 * precision is not specified, a default `precision` of zero is used.
	 */
	public static function number_round(
		NumberValue $n,
		NumberValue $precision = \null
	): NumberValue {
		return NumberValue::build((string) \round(
			(float) $n->value,
			$precision ? (int) $precision->value : 0
		));
	}

	/**
	 * Returns the absolute value of number `n`.
	 */
	public static function number_abs(NumberValue $n): NumberValue {
		return NumberValue::build((string) \abs((float) $n->value));
	}

	/**
	 * Returns number `n` rounded up.
	 */
	public static function number_ceil(NumberValue $n): NumberValue {
		return NumberValue::build((string) \ceil((float) $n->value));
	}

	/**
	 * Returns number `n` rounded down.
	 */
	public static function number_floor(NumberValue $n): NumberValue {
		return NumberValue::build((string) \floor((float) $n->value));
	}

	/**
	 * Returns the square root of a number `n`.
	 */
	public static function number_sqrt(NumberValue $n): NumberValue {
		return new NumberValue((string) \bcsqrt(
			$n->value,
			NumberValue::PRECISION
		));
	}

	/**
	 * Returns number `n` squared to the power of `power`.
	 */
	public static function number_pow(
		NumberValue $n,
		?NumberValue $power = \null
	): NumberValue {

		/** @var NumberValue */
		$result = $n->doPower($power ?? new NumberValue('2'));
		return $result;

	}

	/**
	 * Returns the sine of number `n` specified in radians.
	 */
	public static function number_sin(NumberValue $n): NumberValue {
		return new NumberValue((string) \sin((float) $n->value));
	}

	/**
	 * Returns the cosine of number `n` specified in radians.
	 */
	public static function number_cos(NumberValue $n): NumberValue {
		return new NumberValue((string) \cos((float) $n->value));
	}

	/**
	 * Returns the tangent of number `n` specified in radians.
	 */
	public static function number_tan(NumberValue $n): NumberValue {
		return new NumberValue((string) \tan((float) $n->value));
	}

	/**
	 * Returns the arc tangent of number `n` specified in radians.
	 */
	public static function number_atan(NumberValue $n): NumberValue {
		return new NumberValue((string) \atan((float) $n->value));
	}

	/**
	 * Returns the remainder (modulo) of the division of the arguments.
	 */
	public static function number_mod(
		NumberValue $a,
		NumberValue $b
	): NumberValue {
		return new NumberValue((string) ((int) $a->value % (int) $b->value));
	}

	/**
	 * Return `true` if first argument is divisible by the second argument.
	 */
	public static function number_divisible_by(
		NumberValue $a,
		NumberValue $b
	): BoolValue {
		$truth = ((int) $a->value % (int) $b->value) === 0;
		return BoolValue::build($truth);
	}

}
