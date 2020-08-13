<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\StrictObject;
use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Ex\RelationError;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\BoolValue;

use function \Smuuf\Primi\Helpers\yield_left_to_right as primifn_yield_left_to_right;

class ComparisonLTR extends StrictObject {

	public static function handle(
		array $node,
		Context $context
	): Value {

		$gen = primifn_yield_left_to_right($node, $context);

		$result = \true;
		$left = $gen->current();
		$gen->next();

		while ($gen->valid()) {

			[$op, $right] = $gen->current();
			$result &= static::evaluate($op, $left, $right);
			$gen->next();

			$left = $right;

		}

		return new BoolValue((bool) $result);

	}

	public static function evaluate(
		string $op,
		Value $left,
		Value $right
	): bool {

		switch ($op) {
			case '==':
			case '!=':
				return self::evaluateEquality($op, $left, $right);
			case '>':
			case '<':
			case '>=':
			case '<=':
				return self::evaluateRelation($op, $left, $right);
			default:
				throw new EngineError("Unknown operator '$op'");
		}

	}

	private static function evaluateEquality(
		string $op,
		Value $left,
		Value $right
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
		Value $left,
		Value $right
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

}

