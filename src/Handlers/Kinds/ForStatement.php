<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Compiler\MetaFlag;
use Smuuf\Primi\Handlers\Handler;
use Smuuf\Primi\VM\Machine;

/**
 * Node fields:
 * left: A source iterator.
 * item: Variable name to store the single item in.
 * right: Node representing contents of code to execute while iterating the iterator structure.
 */
class ForStatement extends Handler {

	public static function compile(Compiler $bc, array $node) {

		$iterLabel = $bc->createLabel();
		$breakLabel = $bc->createLabel();
		$finishLabel = $bc->createLabel();

		$bc->inject($node['left']);
		$bc->add(Machine::OP_ITER_GET);
		$bc->insertLabel($iterLabel);

		// This retrieves next value from the iterator on the top of the stack
		// and if it detects the iterator is depleted, jumps to the finish.
		$bc->add(Machine::OP_ITER_NEXT, $finishLabel);

		$bc->withMetaFrame(function() use ($bc, $node) {

			$bc->inject($node['targets']);
			$targetNames = $bc->getMeta(MetaFlag::TargetNames);

			if (count($targetNames) > 1) {
				$bc->add(Machine::OP_UNPACK_SEQUENCE, count($targetNames));
			}

			foreach ($targetNames as $targetName) {
				$bc->add(Machine::OP_STORE_NAME, $targetName);
			}

		});

		$bc->withMetaFrame(function() use ($bc, $node, $iterLabel, $breakLabel) {

			$bc->setMeta(MetaFlag::InLoop, true);
			$bc->setMeta(MetaFlag::ContinueJumpTargetLabel, $iterLabel);
			$bc->setMeta(MetaFlag::BreakJumpTargetLabel, $breakLabel);

			// Inject opcodes from the body.
			$bc->inject($node['right']);

		});

		// Jump back to the OP_ITER_GET label (which expects the iterator we're
		// just iterating on the top of the stack.)
		$bc->add(Machine::OP_JUMP, $iterLabel);

		// If the iterator ended prematurely via break - pop the iterator off
		// the value stack (if the iterator finishes normally, the same is
		// done by the OP_ITER_NEXT opcode in VM).
		$bc->insertLabel($breakLabel);
		$bc->add(Machine::OP_POP);

		$bc->insertLabel($finishLabel);

	}

}
