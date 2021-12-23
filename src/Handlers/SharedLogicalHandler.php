<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\EngineInternalError;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Interned;

/**
 * Common ancestor of LogicalAnd and LogicalOr handlers, both of which have
 * almost exact same implementation, but are separated on a grammar level for
 * operators "and" and "or" to have a distinct precedences.
 *
 * Both "and" and "or" operators do support short-circuiting.
 */
abstract class SharedLogicalHandler extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		$type = $node['type'];

		if ($type === "and") {
			return self::handleAnd($node, $context);
		}

		if ($type === "or") {
			return self::handleOr($node, $context);
		}

		// Unknown operator - should not ever happen, unless there's
		// any unexpected output of source code parting.
		throw new EngineInternalError("Unknown operator '$type'");

	}

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

	/**
	 * @param TypeDef_AstNode $node
	 */
	private static function handleAnd(
		array $node,
		Context $context
	): BoolValue {

		$gen = Func::yield_left_to_right($node, $context);
		foreach ($gen as [$_, $operand]) {

			// Short-circuiting OR operator: if any of the results is already
			// true, do not do the rest.
			if (!$operand->isTruthy()) {
				return Interned::bool(\false);
			}

		}

		return Interned::bool(\true);

	}

	/**
	 * @param TypeDef_AstNode $node
	 */
	private static function handleOr(
		array $node,
		Context $context
	): AbstractValue {

		$gen = Func::yield_left_to_right($node, $context);
		foreach ($gen as [$_, $operand]) {

			// Short-circuiting OR operator: if any of the results is already
			// truthy, do not do the rest and return the first truthy value.
			if ($operand->isTruthy()) {
				return $operand;
			}

		}

		return Interned::bool(\false);

	}

}
