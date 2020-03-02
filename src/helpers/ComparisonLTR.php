<?php

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\ISupportsComparison;
use \Smuuf\Primi\InternalBinaryOperationException;

class ComparisonLTR extends \Smuuf\Primi\StrictObject {

	const SHORT_CIRCUIT = true;

	public static function evaluate(
		string $op,
		Value $left,
		Value $right
	): value {

		try {

			if (!$left instanceof ISupportsComparison) {
				throw new \TypeError;
			}

			if (!$right instanceof ISupportsComparison) {
				throw new \TypeError;
			}

			return $left->doComparison($op, $right);

		} catch (\TypeError $e) {
			throw new InternalBinaryOperationException($op, $left, $right);
		}

	}

	public static function handle(array $node, Context $context): BoolValue {

		$operands = $node['operands'];
		$lastRight = false;
		$result = true;

		foreach ($node['ops'] as $index => $opNode) {

			$lOperandNode = $operands[$index];
			$rOperandNode = $operands[$index + 1];

			// Optimization: If we're not in the first iteration, do not
			// evaluate the previously 'right' expression node again, but just
			// reuse it as 'left' now.
			$left = $lastRight ?: HandlerFactory::get($lOperandNode['name'])
				::handle($lOperandNode, $context);
			$right = HandlerFactory::get($rOperandNode['name'])
				::handle($rOperandNode, $context);

			$resultValue = static::evaluate($opNode['text'], $left, $right);
			$result &= Common::isTruthy($resultValue);

			// Short-circuiting, if any of the results is already false.
			if (!$result) {
				return new BoolValue(false);
			}

			$lastRight = $right;

		}

		return new BoolValue((bool) $result);

	}

}

