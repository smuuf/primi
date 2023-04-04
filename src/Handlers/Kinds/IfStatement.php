<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;

/**
 * Node fields:
 * left: A comparison expression node.
 * right: Node representing contents of code to execute if left-hand result is truthy.
 */
class IfStatement extends Handler {

	public static function reduce(array &$node): void {

		$elifs = [];
		if (isset($node['elifCond'])) {

			$node['elifCond'] = Func::ensure_indexed($node['elifCond']);
			$node['elifBlock'] = Func::ensure_indexed($node['elifBlock'] ?? []);

			foreach ($node['elifCond'] as $i => $elifCond) {
				$elifs[] = [
					'cond' => $elifCond,
					'block' => $node['elifBlock'][$i],
				];
			}

			unset($node['elifCond']);
			unset($node['elifBlock']);

		}

		$node['elifs'] = $elifs;

	}

	public static function compile(Compiler $bc, array $node): void {

		$endLabel = $bc->createLabel();
		$nextLabel = $bc->createLabel();

		$cond = $node['cond'];
		$block = $node['block'];

		$bc->inject($cond);
		$bc->add(Machine::OP_JUMP_IF_F, $nextLabel);
		$bc->inject($block);
		$bc->add(Machine::OP_JUMP, $endLabel);
		$bc->insertLabel($nextLabel);

		$elifs = $node['elifs'] ?? [];
		foreach ($elifs as ['cond' => $cond, 'block' => $block]) {
			$nextLabel = $bc->createLabel();
			$bc->inject($cond);
			$bc->add(Machine::OP_JUMP_IF_F, $nextLabel);
			$bc->inject($block);
			$bc->add(Machine::OP_JUMP, $endLabel);
			$bc->insertLabel($nextLabel);
		}

		if (isset($node['elseBlock'])) {
			$bc->inject($node['elseBlock']);
		}
		$bc->insertLabel($endLabel);

	}

}
