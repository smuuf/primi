<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Ex\EngineInternalError;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Helpers\Interned;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;

class Comparison extends Handler {

	public static function reduce(array &$node): void {

		// If there is no operator, that means there's only one operand.
		// In that case, return only the operand node inside.
		if (!isset($node['ops'])) {
			$node = $node['operands'];
		} else {
			$node['ops'] = Func::ensure_indexed($node['ops']);
		}

	}

	public static function compile(Compiler $bc, array $node): void {

		$finishLabel = $bc->createLabel();
		$operandCount = count($node['operands']);

		// Special case for comparison of only two operands (in that case
		// we don't need to keep track of the previous operand for next
		// comparison in chain).
		if ($operandCount === 2) {

			$ltr = Func::yield_nodes_left_to_right($node);
			foreach ($ltr as [$operator, $operand]) {

				$bc->inject($operand);
				if ($operator === \null) {
					continue;
				}

				self::compileOperator($bc, $operator);

			}

			return;

		}

		// We need to know when we get the last operand.
		$operandNumber = 0;
		$cleanupLabel = $bc->createLabel();

		$ltr = Func::yield_nodes_left_to_right($node);
		foreach ($ltr as [$operator, $operand]) {

			$operandNumber++;

			$bc->inject($operand);
			if ($operator === \null) {
				continue;
			}

			// Do this before each of the comparison except the last.
			if ($operandNumber !== $operandCount) {
				// Swap the top 2 values (operands) in the stack, so when
				// we duplicate it, the second one remains there even after
				// we perform our comparison. That's because we support chained
				// comparisons, e.g. "1 < 2 < c > 3" and need to keep a value
				// for next comparison in the chain.
				$bc->add(Machine::OP_SWAP, 2);
				$bc->add(Machine::OP_COPY, 2);
			}

			self::compileOperator($bc, $operator);

			// Do this before each of the comparison except the last.
			if ($operandNumber !== $operandCount) {
				// If the comparison was not successful, we're going to jump
				// to the end of the whole chain to finish up.
				// This is our short-circuiting.
				// And if the comparison returned `true`, we're going to pop
				// that result from stack, because we're not going to need it
				// anymore - we've got more comparisons to do.
				$bc->add(Machine::OP_JUMP_IF_F_OR_POP, $cleanupLabel);
			}

		}

		$bc->add(Machine::OP_JUMP, $finishLabel);
		$bc->insertLabel($cleanupLabel);
		$bc->add(Machine::OP_SWAP, 2);
		$bc->add(Machine::OP_POP);
		$bc->insertLabel($finishLabel);

	}

	private static function compileOperator(
		Compiler $bc,
		string $operator,
	): void {

		switch (\true) {
			case $operator === '==':
				$bc->add(Machine::OP_COMPARE_EQ);
				break;
			case $operator === '!=':
				$bc->add(Machine::OP_COMPARE_NEQ);
				break;
			case $operator === 'in':
				$bc->add(Machine::OP_COMPARE_IN, 'in');
				break;
			case $operator === 'not in':
				$bc->add(Machine::OP_COMPARE_IN, 'not in');
				$bc->add(Machine::OP_NEGATE);
				break;
			case $operator === '>':
			case $operator === '<':
			case $operator === '>=':
			case $operator === '<=':
				$bc->add(Machine::OP_COMPARE_RELATION, $operator);
				break;
			default:
				$msg = "Unknown operator '$operator'";
				throw new EngineInternalError($msg);
		}

	}

	public static function handleEqual(
		AbstractValue $left,
		AbstractValue $right,
	): AbstractValue {

		// Compare identity first - if both operands are the same object, no
		// need to compare them any further.
		if ($left === $right) {
			return Interned::bool(\true);
		}

		// If the left side doesn't know how to evaluate equality with the right
		// side (the first call returned null), switch operands and try again.
		// If both sides did not know how to evaluate equality with themselves,
		// the equality is false.
		$result = $left->isEqualTo($right)
			?? $right->isEqualTo($left)
			?? \false;

		return Interned::bool($result);

	}

	public static function handleRelation(
		AbstractValue $left,
		AbstractValue $right,
		string $op,
	): AbstractValue {

		$result = $left->hasRelationTo($op, $right);

		// If the left side didn't know how to evaluate relation with the right
		// side (the hasRelationTo call returned null), the relation is
		// undefined and thus raises an error.
		if ($result === \null) {
			Exceptions::piggyback(
				StaticExceptionTypes::getTypeErrorType(),
				"Undefined relation '$op' between '{$left->getTypeName()}'"
				. " and '{$right->getTypeName()}'",
			);
		}

		return Interned::bool($result);

	}

	public static function handleIn(
		AbstractValue $left,
		AbstractValue $right,
		string $op,
	): AbstractValue {

		// Note the apparently switched operands: A in B means asking if B
		// contains A.
		$result = $right->doesContain($left);

		// If the left side didn't know how to evaluate relation with the right
		// side (the hasRelationTo call returned null), the relation is
		// undefined and thus raises an error.
		if ($result === \null) {
			Exceptions::piggyback(
				StaticExceptionTypes::getTypeErrorType(),
				"Undefined relation '$op' between '{$left->getTypeName()}'"
				. " and '{$right->getTypeName()}'",
			);
		}

		return Interned::bool($result);

	}

}
