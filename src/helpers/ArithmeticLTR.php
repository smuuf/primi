<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\StrictObject;
use \Smuuf\Primi\ISupportsPower;
use \Smuuf\Primi\ISupportsAddition;
use \Smuuf\Primi\ISupportsDivision;
use \Smuuf\Primi\ISupportsSubtraction;
use \Smuuf\Primi\ISupportsMultiplication;
use \Smuuf\Primi\Ex\BinaryOperationError;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Structures\Value;

class ArithmeticLTR extends StrictObject {

	public static function handle(
		array $node,
		Context $context
	): Value {

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
		Value $left,
		Value $right
	): Value {

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
				case $op === "+" && $a instanceof ISupportsAddition:
					$result = $a->doAddition($b);
					break;
				case $op === "-" && $a instanceof ISupportsSubtraction:
					$result = $a->doSubtraction($b);
					break;
				case $op === "*" && $a instanceof ISupportsMultiplication:
					$result = $a->doMultiplication($b);
					break;
				case $op === "/" && $a instanceof ISupportsDivision:
					$result = $a->doDivision($b);
					break;
				case $op === "**" && $a instanceof ISupportsPower:
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

