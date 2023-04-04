<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Ex\EngineInternalError;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Compiler\Compiler;

/**
 * Common ancestor of LogicalAnd and LogicalOr handlers, both of which have
 * almost exact same implementation, but are separated on a grammar level for
 * operators "and" and "or" to have a distinct precedences.
 *
 * Both "and" and "or" operators do support short-circuiting.
 */
abstract class SharedLogicalHandler extends Handler {

	public static function reduce(array &$node): void {

		// If there is no operator "and"/"or", reduce the node to it's only
		// operand, because no "logical" operation is necessary during runtime..
		if (!isset($node['ops'])) {
			$node = $node['operands'];
		} else {

			// Even though operators are the same under a single logical
			// node, the "ops" list is expected by the Func::yield_left_to_right
			// helper, so we need to keep it.
			$node['ops'] = Func::ensure_indexed($node['ops']);

			// The type of operator will not ever change in a single logical
			// operation node - let's extract it right now for easy access
			// during runtime.
			$node['type'] = $node['ops'][0]['text'];

		}

	}

	public static function compile(Compiler $bc, array $node): void {

		$type = $node['type'];

		if ($type === "and") {
			self::compileAnd($bc, $node);
			return;
		}

		if ($type === "or") {
			self::compileOr($bc, $node);
			return;
		}

		// Unknown operator - should not ever happen, unless there's
		// any unexpected output of source code parting.
		throw new EngineInternalError("Unknown operator '$type'");

	}

	/**
	 * @param array $node
	 * @phpstan-phpstan-param TypeDef_AstNode $node
	 */
	private static function compileAnd(
		Compiler $bc,
		array $node,
	): void {

		$labelFinish = $bc->createLabel();

		$gen = Func::yield_nodes_left_to_right($node);
		foreach ($gen as [$_, $operand]) {

			// Run opcodes for each of the operands...
			$bc->inject($operand);

			// If any of the operands returns falsey value, don't do the rest.
			// That's our short-circuiting.
			$bc->add(Machine::OP_JUMP_IF_F_OR_POP, $labelFinish);

		}

		// Pop the last conditional jump opcode, as it is useless.
		$bc->pop();
		$bc->insertLabel($labelFinish);

	}

	/**
	 * @param array $node
	 * @phpstan-phpstan-param TypeDef_AstNode $node
	 */
	private static function compileOr(
		Compiler $bc,
		array $node,
	): void {

		$labelFinish = $bc->createLabel();

		$gen = Func::yield_nodes_left_to_right($node);
		foreach ($gen as [$_, $operand]) {

			// Run opcodes for each of the operands...
			$bc->inject($operand);

			// If any of the operands returns a truthy value, don't do the rest.
			// That's our short-circuiting.
			$bc->add(Machine::OP_JUMP_IF_T_OR_POP, $labelFinish);

		}

		// Pop the last conditional jump opcode, as it is Useless.
		$bc->pop();
		$bc->insertLabel($labelFinish);

	}

}
