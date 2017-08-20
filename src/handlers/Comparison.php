<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\UnsupportedOperationException;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\ISupportsComparison;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

/**
 * Node fields:
 * left: A "+" or "-" sign signalling the 'side' of the first operand.
 * right: List of operand nodes.
 */
class Comparison extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {

		// Execute the left-hand node and get its return value.
		$leftHandler = HandlerFactory::get($node['left']['name']);
		$leftReturn = $leftHandler::handle($node['left'], $context);

		$rightHandler = HandlerFactory::get($node['right']['name']);
		$rightReturn = $rightHandler::handle($node['right'], $context);

		$op = $node['op']['text'];

		try {

			if ($leftReturn instanceof ISupportsComparison && $rightReturn instanceof Value) {
				return $leftReturn->doComparison($op, $rightReturn);
			} else {
				throw new UnsupportedOperationException;
			}

		} catch (UnsupportedOperationException $e) {

			throw new ErrorException(sprintf(
				"Cannot compare: '%s' and '%s'",
				$leftReturn::TYPE,
				$rightReturn::TYPE
			), $node);

		}

	}

}
