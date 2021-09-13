<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RelationError;
use \Smuuf\Primi\Ex\EngineInternalError;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Func;

use \Smuuf\StrictObject;

class ComparisonLTR {

	use StrictObject;

	public static function handle(
		array $node,
		Context $context
	): AbstractValue {

		$result = \true;

		// This shouldn't be necessary, since the first operator yielded below
		// will always be null - and the result would be set to be the first
		// operand, but let's make static analysis happy.
		$left = Interned::null();

		$gen = Func::yield_left_to_right($node, $context);
		foreach ($gen as [$operator, $right]) {

			if ($operator === \null) {
				$left = $right;
				continue;
			}

			$result &= static::evaluate($operator, $left, $right);
			$left = $right;

		}

		return Interned::bool((bool) $result);

	}

	public static function evaluate(
		string $op,
		AbstractValue $left,
		AbstractValue $right
	): bool {

		switch (\true) {
			case $op === '==':
				return self::evaluateEqual($left, $right);
			case $op === '!=':
				return self::evaluateNotEqual($left, $right);
			case $op === 'in':
				return self::evaluateIn($op, $left, $right);
			case $op === 'not in':
				return !self::evaluateIn($op, $left, $right);
			case $op === '>':
			case $op === '<':
			case $op === '>=':
			case $op === '<=':
				return self::evaluateRelation($op, $left, $right);
			default:
				throw new EngineInternalError("Unknown operator '$op'");
		}

	}

	private static function evaluateEqual(
		AbstractValue $left,
		AbstractValue $right
	): bool {

		// Compare identity first - if both operands are the same object, no
		// need to compare them any further.
		if ($left === $right) {
			return true;
		}

		// If the left side doesn't know how to evaluate equality with the right
		// side (the first call returned null), switch operands and try again.
		// If both sides did not know how to evaluate equality with themselves,
		// the equality is false.
		return $left->isEqualTo($right)
			?? $right->isEqualTo($left)
			?? \false;

	}

	private static function evaluateNotEqual(
		AbstractValue $left,
		AbstractValue $right
	): bool {

		// Compare identity first - if both operands are the same object, no
		// need to compare them any further.
		if ($left === $right) {
			return false;
		}

		// If the left side doesn't know how to evaluate equality with the right
		// side (the first call returned null), switch operands and try again.
		// If both sides did not know how to evaluate equality with themselves,
		// the equality is false.
		$result = $left->isEqualTo($right)
			?? $right->isEqualTo($left)
			?? \false;

		return !$result;

	}

	private static function evaluateRelation(
		string $op,
		AbstractValue $left,
		AbstractValue $right
	): bool {

		$result = $left->hasRelationTo($op, $right);

		// If the left side didn't know how to evaluate relation with the right
		// side (the hasRelationTo call returned null), the relation is
		// undefined and thus raises an error.
		if ($result === \null) {
			throw new RelationError($op, $left, $right);
		}

		return $result;

	}

	private static function evaluateIn(
		string $op,
		AbstractValue $left,
		AbstractValue $right
	): bool {

		// Note the apparently switched operands: A in B means asking if B
		// contains A.
		$result = $right->doesContain($left);

		// If the left side didn't know how to evaluate relation with the right
		// side (the hasRelationTo call returned null), the relation is
		// undefined and thus raises an error.
		if ($result === \null) {
			throw new RelationError($op, $left, $right);
		}

		return $result;

	}

}

