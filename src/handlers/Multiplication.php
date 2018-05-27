<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\InternalBinaryOperationxception;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Helpers\BinaryOpHandler;

/**
 * Node fields:
 * operands: List of operand nodes.
 * ops: List of nodes acting as operators between the operands.
 */
class Multiplication extends \Smuuf\Primi\StrictObject implements IHandler, IReducer {

	public static function handle(array $node, Context $context) {

		try {

			return BinaryOpHandler::handle($node, $context);

		} catch (InternalBinaryOperationxception $e) {

			throw new ErrorException(sprintf(
				"Cannot %s types '%s' and '%s'",
				$e->getOperator() === "*" ? "multiply" : "divide",
				($e->getLeft())::TYPE,
				($e->getRight())::TYPE
			), $node);

		}

	}

	public static function reduce(array $node) {

		// No need to represent this kind of node as Multiplication when there's only one operand.
		// Take the only operand here and subtitute the Multiplication node with it.
		if (isset($node['operands']['name'])) {
			return $node['operands'];
		}

	}

}
