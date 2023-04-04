<?php

declare(strict_types=1);

namespace Smuuf\Primi\Compiler\PostProcessors\Optimizers;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Ex\PiggybackException;
use Smuuf\Primi\Ex\EngineInternalError;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Helpers\Interned;
use Smuuf\Primi\Compiler\Op;
use Smuuf\Primi\Compiler\BytecodeDLL;
use Smuuf\Primi\Compiler\PostProcessors\Peephole\PeepholePattern;

/**
 * Constant folding optimizer.
 *
 * Converts:
 *
 * ```
 * OP_LOAD_CONST 12
 * OP_LOAD_CONST 34
 * OP_SUB
 * OP_LOAD_CONST "abc"
 * OP_LOAD_CONST "def"
 * OP_ADD
 * ```
 *
 * Into:
 *
 * ```
 * OP_LOAD_CONST -22
 * OP_LOAD_CONST "abcdef"
 * ```
 */
class ConstantFolding implements OptimizerInterface {

	use \Smuuf\StrictObject;

	private const FOLDING_OPCODES = [
		Machine::OP_ADD,
		Machine::OP_SUB,
		Machine::OP_MULTI,
		Machine::OP_DIV,
		Machine::OP_EXP,
		Machine::OP_COMPARE_EQ,
		Machine::OP_COMPARE_NEQ,
	];

	public function optimize(BytecodeDLL $bytecode): bool {

		$changed = false;

		$pattern = (new PeepholePattern)
			->add(Machine::OP_LOAD_CONST)
			->add(Machine::OP_LOAD_CONST)
			->add(self::FOLDING_OPCODES);

		foreach ($pattern->scan($bytecode) as $replacer) {

			$replacer(function(array $match) use (&$changed): ?array {

				/** @var array<Op> $match */
				$result = self::handleFolding(
					$match[2]->opType, // Operator instruction.
					$match[0]->args[0], // Operand left.
					$match[1]->args[0], // Operand right.
				);

				$changed |= (bool) $result;

				// Return list of replacement opcodes.
				return $result
					? [$result]
					: null;

			});

		}

		return (bool) $changed;

	}

	private static function handleFolding(
		string $instruction,
		AbstractValue $left,
		AbstractValue $right,
	): ?Op {

		try {

			switch ($instruction) {
				case Machine::OP_ADD:
					$result = $left->doAddition($right);
					break;
				case Machine::OP_SUB:
					$result = $left->doSubtraction($right);
					break;
				case Machine::OP_MULTI:
					$result = $left->doMultiplication($right);
					break;
				case Machine::OP_DIV:
					$result = $left->doDivision($right);
					break;
				case Machine::OP_EXP:
					$result = $left->doPower($right);
					break;
				case Machine::OP_COMPARE_EQ:
					$result = $left->isEqualTo($right);
					if ($result !== null) {
						$result = Interned::bool($result);
					}
					break;
				case Machine::OP_COMPARE_NEQ:
					$result = $left->isEqualTo($right);
					if ($result !== null) {
						$result = Interned::bool(!$result);
					}
					break;
				default:
					throw new EngineInternalError("Cannot fold '$instruction'");
			}

			// Only fold if there was a defined result.
			// For example some operations (like adding list and number)
			// may be undefined (and return null internally) and should
			// throw errors during runtime.
			if ($result !== null) {
				return new Op(opType: Machine::OP_LOAD_CONST, args: [$result]);
			}

		} catch (PiggybackException) {

			// Skip folding if the evaluation resulted in a runtime error.
			// For example some operations (division by zero) can throw a
			// RuntimeError (and we want such errors to happen during runtime
			// and not during compilation).

		}

		return null;

	}

}
