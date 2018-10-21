<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\InternalBinaryOperationException;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Helpers\BinaryLTR;

/**
 * Node fields:
 * operands: List of operand nodes.
 * ops: List of nodes acting as operators between the operands.
 */
class Addition extends \Smuuf\Primi\StrictObject implements IHandler, IReducer {

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

	public static function reduce(array $node) {

		// No need to represent this kind of node as Addition when there's only one operand.
		// Take the only operand here and subtitute the Addition node with it.
		if (!isset($node['ops'])) {
			return $node['operands'];
		}

	}

}
