<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\StrictObject;
use \Smuuf\Primi\Ex\BinaryOperationError;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Values\AbstractValue;

class ArithmeticLTR extends StrictObject {

	public static function handle(
		array $node,
		Context $context
	): AbstractValue {

		$gen = Func::yield_left_to_right($node, $context);

		$result = $gen->current();
		$gen->next();

		while ($gen->valid()) {

			[$op, $next] = $gen->current();
			$result = static::evaluate($op, $result, $next);
			$gen->next();

		}

		return $result;

	}

	public static function evaluate(
		string $op,
		AbstractValue $left,
		AbstractValue $right
	): AbstractValue {

		[$a, $b] = [$left, $right];
		$result = \null;
		$i = 0;

		// We're going to have two tries at this. If the first try returns null,
		// it means that the left side acknowledged it doesn't know how
		// to evaluate the operation. If that's the case, we'll switch the
		// left/right operators and try if the right side does know what
		// to do. If not and it too returns null, NotImplementedException is
		// raised.
		for ($i = 0; $i < 2; $i++) {

			switch (\true) {
				case $op === "+":
					$result = $a->doAddition($b);
					break;
				case $op === "-":
					$result = $a->doSubtraction($b);
					break;
				case $op === "*":
					$result = $a->doMultiplication($b);
					break;
				case $op === "/":
					$result = $a->doDivision($b);
					break;
				case $op === "**":
					$result = $a->doPower($b);
					break;
			}

			if ($result !== \null) {
				break;
			}

			[$b, $a] = [$a, $b];

		}

		if ($result === \null) {
			throw new BinaryOperationError($op, $left, $right);
		}

		return $result;

	}

}
