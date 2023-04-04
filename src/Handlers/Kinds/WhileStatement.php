<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Compiler\MetaFlag;
use Smuuf\Primi\Handlers\Handler;

class WhileStatement extends Handler {

	public static function compile(Compiler $bc, array $node): void {

		$condLabel = $bc->createLabel();
		$endLabel = $bc->createLabel();

		$bc->insertLabel($condLabel);
		$bc->inject($node['left']);
		$bc->add(Machine::OP_JUMP_IF_F, $endLabel);

		$bc->withMetaFrame(function() use ($bc, $node, $condLabel, $endLabel) {

			$bc->setMeta(MetaFlag::InLoop, true);
			$bc->setMeta(MetaFlag::ContinueJumpTargetLabel, $condLabel);
			$bc->setMeta(MetaFlag::BreakJumpTargetLabel, $endLabel);

			$bc->inject($node['right']);

		});

		$bc->add(Machine::OP_JUMP, $condLabel);
		$bc->insertLabel($endLabel);

	}

}
