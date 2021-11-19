<?php

namespace Smuuf\Primi\Stdlib\Modules;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RelationError;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Modules\NativeModule;

/**
 * Native 'std.math' module.
 */
return new class extends NativeModule {

	public function execute(Context $ctx): array {

		return [
			// π
			'PI' => Interned::number('3.14159265358979323846'),
			// Euler's number
			'E' => Interned::number('2.718281828459045'),
		];

	}

	private static function minmax(string $op, array $items): AbstractValue {

		$minmax = $items[0];
		foreach ($items as $item) {

			$rel = $item->hasRelationTo($op, $minmax);

			if ($rel === \null) {
				throw new RelationError($op, $item, $minmax);
			} elseif ($rel) {
				$minmax = $item;
			}

		}

		return $minmax;

	}

	/**
	 * Return the highest value from iterable or from two or more arguments.
	 *
	 * @primi.function(no-stack)
	 */
	public static function max(AbstractValue ...$items): AbstractValue {
		return self::minmax('>', $items);
	}

	/**
	 * Return the highest value from iterable or from two or more arguments.
	 *
	 * @primi.function(no-stack)
	 */
	public static function min(AbstractValue ...$items): AbstractValue {
		return self::minmax('<', $items);
	}

	//
	// Trigonometry functions..
	//

	/**
	 * Returns the sine of number `n` specified in radians.
	 *
	 * @primi.function(no-stack)
	 */
	public static function sin(NumberValue $n): NumberValue {
		$result = (string) \sin((float) $n->value);
		return new NumberValue(Func::scientific_to_decimal($result));
	}

	/**
	 * Returns the cosine of number `n` specified in radians.
	 *
	 * @primi.function(no-stack)
	 */
	public static function cos(NumberValue $n): NumberValue {
		$result = (string) \cos((float) $n->value);
		return new NumberValue(Func::scientific_to_decimal($result));
	}

	/**
	 * Returns the tangent of number `n` specified in radians.
	 *
	 * @primi.function(no-stack)
	 */
	public static function tan(NumberValue $n): NumberValue {
		$result = (string) \tan((float) $n->value);
		return new NumberValue(Func::scientific_to_decimal($result));
	}

	/**
	 * Returns the arc tangent of number `n` specified in radians.
	 *
	 * @primi.function(no-stack)
	 */
	public static function atan(NumberValue $n): NumberValue {
		$result = (string) \atan((float) $n->value);
		return new NumberValue(Func::scientific_to_decimal($result));
	}

	//
	// Rounding.
	//

	/**
	 * Returns number `n` rounded to specified `precision`. If the
	 * precision is not specified, a default `precision` of zero is used.
	 *
	 * @primi.function(no-stack)
	 */
	public static function round(
		NumberValue $n,
		NumberValue $precision = \null
	): NumberValue {
		return Interned::number((string) \round(
			(float) $n->value,
			$precision ? (int) $precision->value : 0
		));
	}

	/**
	 * Returns number `n` rounded up.
	 *
	 * @primi.function(no-stack)
	 */
	public static function ceil(NumberValue $n): NumberValue {
		return Interned::number((string) \ceil((float) $n->value));
	}

	/**
	 * Returns number `n` rounded down.
	 *
	 * @primi.function(no-stack)
	 */
	public static function floor(NumberValue $n): NumberValue {
		return Interned::number((string) \floor((float) $n->value));
	}

	/**
	 * Returns the absolute value of number `n`.
	 *
	 * @primi.function(no-stack)
	 */
	public static function abs(NumberValue $n): NumberValue {
		return Interned::number(\ltrim($n->value, '-'));
	}

	/**
	 * Returns the square root of a number `n`.
	 *
	 * @primi.function(no-stack)
	 */
	public static function sqrt(NumberValue $n): NumberValue {
		return new NumberValue((string) \bcsqrt(
			$n->value,
			NumberValue::PRECISION
		));
	}

	/**
	 * Returns number `n` squared to the power of `power`.
	 *
	 * @primi.function(no-stack)
	 */
	public static function pow(
		NumberValue $n,
		?NumberValue $power = \null
	): NumberValue {

		/** @var NumberValue */
		$result = $n->doPower($power ?? new NumberValue('2'));
		return $result;

	}

	/**
	 * Returns the remainder (modulo) of the division of the arguments.
	 *
	 * @primi.function(no-stack)
	 */
	public static function mod(
		NumberValue $a,
		NumberValue $b
	): NumberValue {
		return new NumberValue((string) ((int) $a->value % (int) $b->value));
	}

};
