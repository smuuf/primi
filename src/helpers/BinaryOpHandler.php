<?php

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

use \Smuuf\Primi\ISupportsAddition;
use \Smuuf\Primi\ISupportsSubtraction;
use \Smuuf\Primi\ISupportsMultiplication;
use \Smuuf\Primi\ISupportsDivision;

use \Smuuf\Primi\InternalBinaryOperationxception;

class BinaryOpHandler extends \Smuuf\Primi\StrictObject {

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
					// We're not throwing InternalBinaryOperationxception
					// becasue we want to unify handling wrong "op" with
					// whatever type errors might be thrown from inside the
					// value's operator methods.
					throw new \TypeError;
			}

		} catch (\TypeError $e) {

			// Handle all type errors in one place.
			throw new InternalBinaryOperationxception($op, $left, $right);

		}

	}

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
			$tmp = $handler::handle($operandNode, $context);

			// Extract the text of the assigned operator node.
			$op = $node['ops'][$index]['text'];
			$result = self::evaluate($op, $result, $tmp);

		}

		return $result;

	}

}

