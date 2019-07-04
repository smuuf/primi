<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Helpers\ComparisonLTR;
use \Smuuf\Primi\InternalBinaryOperationException;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Context;

/**
 * Node fields:
 * left: A "+" or "-" sign signalling the 'side' of the first operand.
 * right: List of operand nodes.
 */
class Comparison extends \Smuuf\Primi\StrictObject implements IHandler, IReducer {

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

	public static function reduce(array $node) {

		// If there is no operator, that means there's only one operand.
		// In that case, return only the operand node inside.
		if (!isset($node['ops'])) {
			return $node['operands'];
		}

	}

}
