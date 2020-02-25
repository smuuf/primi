<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Helpers\ComparisonLTR;
use \Smuuf\Primi\InternalBinaryOperationException;

/**
 * Node fields:
 * left: A "+" or "-" sign signalling the 'side' of the first operand.
 * right: List of operand nodes.
 */
class Comparison extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		try {

			return ComparisonLTR::handle($node, $context);

		} catch (InternalBinaryOperationException $e) {

			throw new ErrorException(sprintf(
				"Cannot compare '%s' with '%s'",
				($e->getLeft())::TYPE,
				($e->getRight())::TYPE
			), $node);

		}

	}

	public static function reduce(array &$node): void {

		// If there is no operator, that means there's only one operand.
		// In that case, return only the operand node inside.
		if (!isset($node['ops'])) {
			$node = $node['operands'];
		} else {
			$node['ops'] = Common::ensureIndexed($node['ops']);
		}

	}

}
