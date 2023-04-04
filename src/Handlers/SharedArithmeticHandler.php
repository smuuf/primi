<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Handlers\Handler;

/**
 * Common ancestor of Addition, Multiplication handlers, both of which have
 * the exact same implementation, but are separated on a grammar level for
 * operators "and" and "or" to have a distinct precedences.
 */
abstract class SharedArithmeticHandler extends Handler {

	public static function reduce(array &$node): void {

		// If there is no operator, then there's no need to keep this as
		// a complex node of this type. Reduce this node to its only operand.
		if (!isset($node['ops'])) {
			$node = $node['operands'];
		} else {
			$node['ops'] = Func::ensure_indexed($node['ops']);
		}

	}

	public static function compile(Compiler $bc, array $node): void {

		$ltr = Func::yield_nodes_left_to_right($node);
		foreach ($ltr as [$operator, $operand]) {

			$bc->inject($operand);

			if ($operator === \null) {
				continue;
			}

			switch ($operator) {
				case '+':
					$bc->add(Machine::OP_ADD);
					break;
				case '-':
					$bc->add(Machine::OP_SUB);
					break;
				case '*':
					$bc->add(Machine::OP_MULTI);
					break;
				case '/':
					$bc->add(Machine::OP_DIV);
					break;
			}

		}

	}

}
