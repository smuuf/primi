<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;

class CondExpr extends Handler {

	public static function reduce(array &$node): void {

		if (!isset($node['cond'])) {
			$node = $node['true'];
		}

	}

	public static function compile(Compiler $bc, array $node) {

		$cond = $node['cond'];
		$trueBlock = $node['true'];
		$elseBlock = $node['false'];
		$elseLabel = $bc->createLabel();
		$endLabel = $bc->createLabel();

		$bc->inject($cond);
		$bc->add(Machine::OP_JUMP_IF_F, $elseLabel);
		$bc->inject($trueBlock);
		$bc->add(Machine::OP_JUMP, $endLabel);
		$bc->insertLabel($elseLabel);
		$bc->inject($elseBlock);
		$bc->insertLabel($endLabel);

	}

}
