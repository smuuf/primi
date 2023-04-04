<?php

declare(strict_types=1);

namespace Smuuf\Primi\VM;

use Smuuf\StrictObject;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Values\AbstractValue;

/**
 * Our VM runtime arithmetics handlers all in one place.
 *
 * @internal
 */
abstract class Arithmetics  {

	use StrictObject;

	public static function add(
		AbstractValue $left,
		AbstractValue $right,
	): AbstractValue {

		$result = $left->doAddition($right);
		if ($result === \null) {
			Exceptions::piggyback(
				StaticExceptionTypes::getRuntimeErrorType(),
				"Cannot use operator '+' with '{$left->getTypeName()}' and '{$right->getTypeName()}'",
			);
		}

		return $result;

	}

	public static function sub(
		AbstractValue $left,
		AbstractValue $right,
	): AbstractValue {

		$result = $left->doSubtraction($right);
		if ($result === \null) {
			Exceptions::piggyback(
				StaticExceptionTypes::getRuntimeErrorType(),
				"Cannot use operator '-' with '{$left->getTypeName()}' and '{$right->getTypeName()}'",
			);
		}

		return $result;

	}

	public static function multi(
		AbstractValue $left,
		AbstractValue $right,
	): AbstractValue {

		$result = $left->doMultiplication($right);
		if ($result === \null) {
			Exceptions::piggyback(
				StaticExceptionTypes::getRuntimeErrorType(),
				"Cannot use operator '*' with '{$left->getTypeName()}' and '{$right->getTypeName()}'",
			);
		}

		return $result;

	}

	public static function div(
		AbstractValue $left,
		AbstractValue $right,
	): AbstractValue {

		$result = $left->doDivision($right);
		if ($result === \null) {
			Exceptions::piggyback(
				StaticExceptionTypes::getRuntimeErrorType(),
				"Cannot use operator '/' with '{$left->getTypeName()}' and '{$right->getTypeName()}'",
			);
		}

		return $result;

	}

	public static function exp(
		AbstractValue $operand,
		AbstractValue $factor,
	): AbstractValue {

		$result = $operand->doPower($factor);
		if ($result === \null) {
			Exceptions::piggyback(
				StaticExceptionTypes::getRuntimeErrorType(),
				"Cannot use operator '**' with '{$operand->getTypeName()}' and '{$factor->getTypeName()}'",
			);
		}

		return $result;

	}

}
