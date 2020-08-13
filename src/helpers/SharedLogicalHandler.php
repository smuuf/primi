<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\RelationError;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\BoolValue;

use function \Smuuf\Primi\Helpers\ensure_indexed as primifn_ensure_indexed;
use function \Smuuf\Primi\Helpers\yield_left_to_right as primifn_yield_left_to_right;

/**
 * Common ancestor of LogicalAnd and LogicalOr handlers, both of which have
 * almost exact same implementation, but are separated on a grammar level for
 * operators "and" and "or" to have a distinct precedences.
 *
 * Both "and" and "or" operators do support short-circuiting.
 */
abstract class SharedLogicalHandler extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		try {

			// The type of operator will not ever change in a single logical
			// operation node.
			$type = $node['ops'][0]['text'];

			switch (\true) {
				case $type === "and":
					return self::handleAnd($node, $context);
				case $type === "or":
					return self::handleOr($node, $context);
				default:
					// Unknown operator - should not ever happen, unless there's
					// any unexpected output of source code parting.
					throw new EngineError("Unknown uperator '$type'.");
			}

		} catch (RelationError $e) {
			throw new RuntimeError($e->getMessage(), $node);
		}

	}

	public static function reduce(array &$node): void {

		if (!isset($node['ops'])) {
			$node = $node['operands'];
		} else {
			$node['ops'] = primifn_ensure_indexed($node['ops']);
		}

	}

	private static function handleAnd(
		array $node,
		Context $context
	): BoolValue {

		$gen = primifn_yield_left_to_right($node, $context);
		$first = $gen->current();

		// If the first operand is truthy, we short-circuit.
		if (!$first->isTruthy()) {
			return new BoolValue(\false);
		}

		$gen->next();
		while ($gen->valid()) {

			[$_, $next] = $gen->current();

			// Short-circuiting OR operator: if any of the results is already
			// true, do not do the rest.
			if (!$next->isTruthy()) {
				return new BoolValue(\false);
			}

			$gen->next();

		}

		return new BoolValue(\true);

	}

	private static function handleOr(
		array $node,
		Context $context
	): Value {

		$gen = primifn_yield_left_to_right($node, $context);
		$first = $gen->current();

		// If the first operand is truthy, we short-circuit.
		if ($first->isTruthy()) {
			return new BoolValue(\true);
		}

		$gen->next();
		while ($gen->valid()) {

			[$_, $next] = $gen->current();

			// Short-circuiting OR operator: if any of the results is already
			// true, do not do the rest.
			if ($next->isTruthy()) {
				return $next;
			}

			$gen->next();

		}

		return new BoolValue(\false);

	}

}
