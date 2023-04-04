<?php

declare(strict_types=1);

namespace Smuuf\Primi\Compiler\PostProcessors\Optimizers;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Compiler\Op;
use Smuuf\Primi\Compiler\BytecodeDLL;
use Smuuf\Primi\Compiler\PostProcessors\Peephole\PeepholePattern;

class UselessCondJump implements OptimizerInterface {

	use \Smuuf\StrictObject;

	private const CONDITIONAL_JUMP_OPS = [
		Machine::OP_JUMP_IF_T,
		Machine::OP_JUMP_IF_F,
	];

	public function optimize(BytecodeDLL $bytecode): bool {

		$pattern = (new PeepholePattern)
			->add(Machine::OP_LOAD_CONST)
			->add(self::CONDITIONAL_JUMP_OPS);

		$changed = false;
		foreach ($pattern->scan($bytecode) as $replacer) {

			$replacer(function(array $match) use (&$changed): array {

				/** @var array<Op> $match */
				$const = $match[0]->args[0];
				$jumpOp = $match[1];
				$result = self::selectSolution($const, $jumpOp);
				$changed = true;

				// Return list of replacement opcodes.
				return $result;

			});

		}

		return (bool) $changed;

	}

	/**
	 * @return list<Op>
	 */
	private static function selectSolution(
		AbstractValue $const,
		Op $jumpOp,
	): array {

		$jumpType = $jumpOp->opType;
		$newOp = match(true) {

			// Would always jump - keep just the jump.
			$const->isTruthy() && $jumpType === Machine::OP_JUMP_IF_T
				=> new Op(
					opType: Machine::OP_JUMP,
					opLoc: $jumpOp->opLoc,
					args: $jumpOp->args
				),

 			// Would always jump - keep just the jump.
			!$const->isTruthy() && $jumpType === Machine::OP_JUMP_IF_F
				=> new Op(
					opType: Machine::OP_JUMP,
					opLoc: $jumpOp->opLoc,
					args: $jumpOp->args
				),

 			// Would never jump - remove both const and the jump.
			$const->isTruthy() && $jumpType === Machine::OP_JUMP_IF_F
				=> null,

 			// Would never jump - remove both const and the jump.
			!$const->isTruthy() && $jumpType === Machine::OP_JUMP_IF_T
				=> null,

			default => null,

		};

		return $newOp
			? [$newOp]
			: [];

	}

}
