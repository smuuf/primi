<?php

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Helpers\BinaryLTR;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\InternalBinaryOperationException;

/**
 * Common ancestor of Addition, Multiplication handlers, both of which have
 * the exact same implementation, but are separated on a grammar level for
 * operators "and" and "or" to have a distinct precedences.
 */
abstract class CommonMathHandler extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		try {

			return BinaryLTR::handle($node, $context);

		} catch (InternalBinaryOperationException $e) {

			throw new ErrorException(sprintf(
				"Cannot use operator '%s' with '%s' and '%s'",
				$e->getOperator(),
				($e->getLeft())::TYPE,
				($e->getRight())::TYPE
			), $node);

		}

	}

	public static function reduce(array $node): ?array {

		// If there is no operator, then there's no need to keep this as
		// a complex node of this type. Reduce this node to its only operand.
		if (!isset($node['ops'])) {
			return $node['operands'];
		}

		return null;

	}

}
