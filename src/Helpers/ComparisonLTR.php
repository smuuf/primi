<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RelationError;
use \Smuuf\Primi\Ex\EngineInternalError;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Traits\StrictObject;

class ComparisonLTR {

	use StrictObject;

	public static function handle(
		array $node,
		Context $context
	): AbstractValue {

		$result = \true;

		$gen = Func::yield_left_to_right($node, $context);
		foreach ($gen as [$operator, $right]) {

			if ($operator === null) {
				$left = $right;
				continue;
			}

			$result &= static::evaluate($operator, $left, $right);
			$left = $right;

		}

		return new BoolValue((bool) $result);

	}

	public static function evaluate(
		string $op,
		AbstractValue $left,
		AbstractValue $right
	): bool {

		switch ($op) {
			case '==':
			case '!=':
				return self::evaluateEquality($op, $left, $right);
			case 'in':
				return self::evaluateIn($op, $left, $right);
			case 'not in':
				return !self::evaluateIn($op, $left, $right);
			case '>':
			case '<':
			case '>=':
			case '<=':
				return self::evaluateRelation($op, $left, $right);
			default:
				throw new EngineInternalError("Unknown operator '$op'");
		}

	}

	private static function evaluateEquality(
		string $op,
		AbstractValue $left,
		AbstractValue $right
	): bool {

		$result = $left->isEqualTo($right);

		// If the left side didn't know how to evaluate equality with the right
		// side (the first call returned null), switch operands and try again.
		if ($result === \null) {
			$result = $right->isEqualTo($left);
		}

		// If both sides did not know how to evaluate equality with themselves,
		// the equality is false.
		$result = $result ?? \false;

		return $op === '=='
			? $result
			: !$result;

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

