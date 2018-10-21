<?php

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\InternalBinaryOperationException;
use \Smuuf\Primi\ISupportsAddition;
use \Smuuf\Primi\ISupportsSubtraction;
use \Smuuf\Primi\ISupportsMultiplication;
use \Smuuf\Primi\ISupportsDivision;
use \Smuuf\Primi\Structures\Value;

class BinaryLTR extends LeftToRightEvaluation {

	public static function evaluate(
		string $op,
		Value $left,
		Value $right
	): value {

		try {

			switch (true) {
				case $op === "+" && $left instanceof ISupportsAddition:
					return $left->doAddition($right);
				case $op === "-" && $left instanceof ISupportsSubtraction:
					return $left->doSubtraction($right);
				case $op === "*" && $left instanceof ISupportsMultiplication:
					return $left->doMultiplication($right);
				case $op === "/" && $left instanceof ISupportsDivision:
					return $left->doDivision($right);
				default:
					// We're not throwing InternalBinaryOperationException
					// because we want to unify handling of wrong "op" with
					// whatever type errors might be thrown from inside the
					// value's operator methods.
					throw new \TypeError;
			}

		} catch (\TypeError $e) {

			// Handle all type errors in one place.
			throw new InternalBinaryOperationException($op, $left, $right);

		}

	}

}

