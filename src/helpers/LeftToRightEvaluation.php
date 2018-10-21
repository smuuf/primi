<?php

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

abstract class LeftToRightEvaluation extends \Smuuf\Primi\StrictObject {

	const SHORT_CIRCUIT = false;

	abstract public static function evaluate(
		string $op,
		Value $left,
		Value $right
	): value;

	public static function handle(array $node, Context $context): Value {

		// Make sure even a single operand can be processed via foreach.
		Common::ensureIndexed($node['ops']);

		$operands = $node['operands'];

		$firstOperand = array_shift($operands);
		$handler = HandlerFactory::get($firstOperand['name']);
		$result = $handler::handle($firstOperand, $context);

		// Go through each of the operands and continuously calculate the result
		// value combining the operand's value with the result-so-far. The
		// operator determining the operands's effect on the result has always
		// the "n" index. (It would be "n-1" but we shifted the first operand
		// already.)
		foreach ($operands as $index => $operandNode) {

			$handler = HandlerFactory::get($operandNode['name']);
			$next = $handler::handle($operandNode, $context);

			// Extract the text of the assigned operator node.
			$op = $node['ops'][$index]['text'];
			$result = static::evaluate($op, $result, $next);

			if (static::SHORT_CIRCUIT && !Common::isTruthy($result)) {
				return $result;
			}

		}

		return $result;

	}

}

