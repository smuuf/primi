<?php

declare(strict_types=1);

namespace Smuuf\Primi\Compiler\PostProcessors;

use Smuuf\Primi\Compiler\BytecodeDLL;
use Smuuf\Primi\Compiler\Label;
use Smuuf\Primi\Compiler\Op;
use Smuuf\Primi\VM\Machine;

class LabelResolver implements PostProcessorInterface {

	use \Smuuf\StrictObject;

	private const OPS_USING_LABELS = [
		Machine::OP_JUMP,
		Machine::OP_JUMP_IF_T,
		Machine::OP_JUMP_IF_F,
		Machine::OP_JUMP_IF_T_OR_POP,
		Machine::OP_JUMP_IF_F_OR_POP,
		// May jump to the end of iteration body.
		Machine::OP_ITER_NEXT,
		// Try-catch handling opcodes that need to jump around.
		Machine::OP_TRYBLK_PUSH,
	];

	/**
	 * Goes through the bytecode, converts labels to no-op instructions,
	 * then removes all no-ops while correctly resolving index of each label.
	 */
	public function process(BytecodeDLL $bytecode): void {

		$labelOffsets = self::gatherLabels($bytecode);
		$noops = 0;

		// First pass:
		// 1. Determine correct index offset for labels based on number of
		// opcodes that are going to be removed in the second pass.
		// 2. Convert all label instructions to no-ops.
		foreach ($bytecode as $i => $op) {
			/** @var Op $op */

			// Convert labels to no-op and correct their offset based on
			// how many no-ops we actually encountered so far.
			if ($op->opType === Machine::OP_LABEL) {
				$bytecode[$i] = $op->with(opType: Machine::OP_NOOP);
				$labelOffsets[$op->args[0]->id] -= $noops;
				$noops++;
			}

			// For each opcode that is going to be removed, all labels
			// after it will have their offset decremented by increasingly
			// higher number (done within the IF above).
			if ($op->opType === Machine::OP_NOOP) {
				$noops++;
			}

		}

		// Second pass.
		$toRemove = [];
		foreach ($bytecode as $i => $op) {
			/** @var Op $op */

			// Remove no-ops from the bytecode.
			if ($op->opType === Machine::OP_NOOP) {
				// Save indices that we will want to remove from the DDL,
				// because doing so while iterating the DDL does weird things.
				$toRemove[] = $i;
				continue;
			}

			// Replace target label ID arguments of jump instructions with the
			// label's corrected index (after offset correction - considering
			// the offsets changed when we removed bunch of no-op instructions
			// before it).
			if (
				!empty($op->args)
				&& \in_array($op->opType, self::OPS_USING_LABELS, \true)
			) {
				$bytecode[$i] = $op->with(
					args: self::resolveLabelObjects($op->args, $labelOffsets),
				);
			}

		}

		// Remove indices we wanted to remove, duh.
		$bytecode->removeIndices($toRemove);

	}

	/**
	 * @return array<string, int>
	 */
	private static function gatherLabels(
		BytecodeDLL $bytecode,
	): array {

		$labels = [];

		foreach ($bytecode as $i => $op) {

			/** @var Op $op */
			// $op->opType: Instruction.
			// $op[1]: Label object.
			if ($op->opType === Machine::OP_LABEL) {
				$labels[$op->args[0]->id] = $i;
			}

		}

		return $labels;

	}

	/**
	 * Recursively search-replace `Label` objects (representing label IDs) with
	 * actual label integer offsets.
	 *
	 * @param list<mixed> $structure
	 * @param array<string, int> $labelOffsets
	 * @return list<mixed>
	 */
	private static function resolveLabelObjects(
		array $structure,
		array $labelOffsets,
	): array {

		array_walk_recursive(
			$structure,
			function(&$value) use ($labelOffsets) {

				if (!$value instanceof Label) {
					return;
				}

				$value = $labelOffsets[$value->id];

			},
		);

		return $structure;

	}

}
