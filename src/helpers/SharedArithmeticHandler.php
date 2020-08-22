<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\BinaryOperationError;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\ArithmeticLTR;
use \Smuuf\Primi\Helpers\SimpleHandler;

/**
 * Common ancestor of Addition, Multiplication handlers, both of which have
 * the exact same implementation, but are separated on a grammar level for
 * operators "and" and "or" to have a distinct precedences.
 */
abstract class SharedArithmeticHandler extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		try {
			return ArithmeticLTR::handle($node, $context);
		} catch (BinaryOperationError $e) {
			throw new RuntimeError(sprintf(
				"Cannot use operator '%s' with '%s' and '%s'",
				$e->getOperator(),
				($e->getLeft())::TYPE,
				($e->getRight())::TYPE
			), $node);
		}

	}

	public static function reduce(array &$node): void {

		// If there is no operator, then there's no need to keep this as
		// a complex node of this type. Reduce this node to its only operand.
		if (!isset($node['ops'])) {
			$node = $node['operands'];
		} else {
			$node['ops'] = Func::ensure_indexed($node['ops']);
		}

	}

}
