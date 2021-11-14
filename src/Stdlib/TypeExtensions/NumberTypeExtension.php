<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Values\TypeValue;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Extensions\TypeExtension;

class NumberTypeExtension extends TypeExtension {

	/**
	 * @primi.function(no-stack)
	 */
	public static function __new__(
		TypeValue $_,
		?AbstractValue $value = \null
	): NumberValue {

		// Default value for a new number is 0.
		if ($value === \null) {
			return Interned::number('0');
		}

		if ($value instanceof BoolValue) {
			return Interned::number($value->isTruthy() ? '1' : '0');
		}

		$number = Func::scientific_to_decimal($value->getStringValue());
		if (!Func::is_decimal($number)) {
			throw new RuntimeError("Invalid number value '$number'");
		}

		return Interned::number($number);

	}

	/**
	 * Return `true` if first argument is divisible by the second argument.
	 *
	 * @primi.function(no-stack)
	 */
	public static function is_divisible_by(
		NumberValue $a,
		NumberValue $b
	): BoolValue {
		$truth = ((int) $a->value % (int) $b->value) === 0;
		return Interned::bool($truth);
	}

}
