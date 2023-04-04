<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use Smuuf\Primi\Extensions\PrimiFunc;
use Smuuf\Primi\Values\BoolValue;
use Smuuf\Primi\Values\TypeValue;
use Smuuf\Primi\Values\NumberValue;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Helpers\Interned;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Extensions\TypeExtension;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Stdlib\StaticTypes;

class NumberTypeExtension extends TypeExtension {

	#[PrimiFunc]
	public static function __new__(
		TypeValue $type,
		?AbstractValue $value = \null
	): NumberValue {

		if ($type !== StaticTypes::getNumberType()) {
			Exceptions::piggyback(
				StaticExceptionTypes::getTypeErrorType(),
				"Passed invalid type object",
			);
		}

		// Default value for a new number is 0.
		if ($value === \null) {
			return Interned::number('0');
		}

		if ($value instanceof BoolValue) {
			return Interned::number($value->isTruthy() ? '1' : '0');
		}

		$number = Func::scientific_to_decimal($value->getStringValue());
		if (!Func::is_decimal($number)) {
			Exceptions::piggyback(
				StaticExceptionTypes::getRuntimeErrorType(),
				"Invalid number value '$number'",
			);
		}

		return Interned::number($number);

	}

	/**
	 * Return `true` if first argument is divisible by the second argument.
	 */
	#[PrimiFunc]
	public static function is_divisible_by(
		NumberValue $a,
		NumberValue $b
	): BoolValue {
		$truth = ((int) $a->value % (int) $b->value) === 0;
		return Interned::bool($truth);
	}

}
