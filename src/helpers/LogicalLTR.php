<?php

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\InternalBinaryOperationException;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Helpers\Common;

class LogicalLTR extends LeftToRightEvaluation {

	const SHORT_CIRCUIT = true;

	public static function evaluate(
		string $op,
		Value $left,
		Value $right
	): value {

		$l = Common::isTruthy($left);
		$r = Common::isTruthy($right);

		switch (true) {
			case $op === "and":
				return new BoolValue($l && $r);
			case $op === "or":
				return new BoolValue($l || $r);
			default:
				// Unknown operator.
				throw new InternalBinaryOperationException($op, $left, $right);
		}

	}

}

