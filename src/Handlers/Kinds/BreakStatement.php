<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Ex\InternalSyntaxError;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Compiler\MetaFlag;
use Smuuf\Primi\Handlers\Handler;

class BreakStatement extends Handler {

	public static function compile(Compiler $bc, array $node) {

		if (!$bc->getMeta(MetaFlag::InLoop, false)) {
			throw InternalSyntaxError::fromNode($node, "'break' used outside loop");
		}

		$break = $bc->getMeta(MetaFlag::BreakJumpTargetLabel);
		$bc->add(Machine::OP_JUMP, $break);

	}
}
