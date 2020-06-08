<?php

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\UndefinedRelationException;

class ComparisonLTR extends \Smuuf\Primi\StrictObject {

	const SHORT_CIRCUIT = true;

	public static function handle(array $node, Context $context): BoolValue {

		$operands = $node['operands'];
		$lastRight = false;
		$result = true;

		try {

			foreach ($node['ops'] as $index => $opNode) {

				$leftNode = $operands[$index];
				$rightNode = $operands[$index + 1];

				// Optimization: If we're not in the first iteration, do not
				// evaluate the previously 'right' expression node again, but just
				// reuse it as 'left' now.
				$left = $lastRight ?: HandlerFactory::get($leftNode['name'])
					::handle($leftNode, $context);
				$right = HandlerFactory::get($rightNode['name'])
					::handle($rightNode, $context);

				$resultValue = static::evaluate($opNode['text'], $left, $right);
				$result &= $resultValue->isTruthy();

				// Short-circuiting, if any of the results is already false.
				if (!$result) {
					return new BoolValue(false);
				}

				$lastRight = $right;

			}

		} catch (UndefinedRelationException $e) {
			throw new ErrorException($e->getMessage(), $node);
		}

		return new BoolValue((bool) $result);

	}

	public static function evaluate(
		string $op,
		Value $left,
		Value $right
	): Value {

		switch ($op) {
			case '==':
			case '!=':
				return new BoolValue(self::evaluateEquality($op, $left, $right));
			case '>':
			case '<':
			case '>=':
			case '<=':
				return new BoolValue(self::evaluateRelation($op, $left, $right));
			default:
				throw new ErrorException("Unknown operator '$op'");
		}

	}

	public static function evaluateEquality(
		string $op,
		Value $left,
		Value $right
	): bool {

		$result = $left->isEqualTo($right);

		// If the left side didn't know how to evaluate equality with the right
		// side (the first call returned null), switch operands and try again.
		if ($result === null) {
			$result = $right->isEqualTo($left);
		}

		// If both sides did not know how to evaluate equality with themselves,
		// the equality is false.
		$result = $result ?? false;

		return $op === '=='
			? $result
			: !$result;

	}

	public static function evaluateRelation(
		string $op,
		Value $left,
		Value $right
	): bool {

		$result = $left->hasRelationTo($op, $right);

		// If the left side didn't know how to evaluate equality with the right
		// side (the first call returned null), switch operands and try again.
		if ($result === null) {
			throw new UndefinedRelationException($op, $left, $right);
		}

		return $result;

	}


}

