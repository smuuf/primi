<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\Ex\EngineInternalError;
use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Handlers\Handler;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Compiler\MetaFlag;

class Assignment extends Handler {

	public static function compile(Compiler $bc, array $node): void {

		$bc->inject($node['right']);

		// Duplicate the value on the stack so that assignment expression itself
		// has a result, so that things like "a = (b = 1)", "a = b.c = 1" or
		// "a = b[c] = 1" work.
		$bc->add(Machine::OP_DUP_TOP);

		$targetNames = [];
		$bc->withMetaFrame(function() use ($bc, $node, &$targetNames) {

			$bc->inject($node['left']);
			$targetNames = $bc->getMeta(MetaFlag::TargetNames, []);

		});

		// If the left node was actually a list of assignment targets, we
		// should unpack the (supposedly) iterable on the right side into
		// separate variables.
		if ($targetNames) {
			$bc->add(Machine::OP_UNPACK_SEQUENCE, count($targetNames));
			foreach ($targetNames as $name) {
				$bc->add(Machine::OP_STORE_NAME, $name);
			}
			return;
		}

		// By popping and inspecting the latest opcode the left AST node
		// emitted we can determine if the assignment is supposed to be into a
		// simple variable name, or into some object's attribute, or into some
		// object as item (bracket[] access).
		$last = $bc->pop();

		switch (true) {
			case $last->opType === Machine::OP_NOOP:
				$bc->add(Machine::OP_STORE_NAME, $last->args[0]);
				break;
			case $last->opType === Machine::OP_LOAD_ATTR:
				$bc->add(Machine::OP_STORE_ATTR, $last->args[0]);
				break;
			case $last->opType === Machine::OP_LOAD_ITEM:
				$bc->add(Machine::OP_STORE_ITEM, $last->args[0] ?? 0);
				break;
			default:
				throw new EngineInternalError("Invalid assignment target");
		}

	}

}
