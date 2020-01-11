<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\InternalUndefinedTruthnessException;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

/**
 * Node fields:
 * left: A comparison expression node.
 * right: Node representing contents of code to execute if left-hand result is truthy.
 */
class IfStatement extends \Smuuf\Primi\StrictObject implements IHandler {

	public static function handle(array $node, Context $context) {

		// Execute the left-hand node and get its return value.
		$leftHandler = HandlerFactory::get($node['left']['name']);
		$return = $leftHandler::handle($node['left'], $context);

		try {

			// If the result of the left hand equals to truthy value,
			// execute the code branch stored in the right-hand node.
			if (Common::isTruthy($return)) {
				$rightHandler = HandlerFactory::get($node['right']['name']);
				$rightHandler::handle($node['right'], $context);
			} elseif (isset($node['else'])) {
				$elseHandler = HandlerFactory::get($node['else']['name']);
				$elseHandler::handle($node['else'], $context);
			}

		} catch (InternalUndefinedTruthnessException $e) {
			throw new ErrorException($e->getMessage(), $node);
		}

	}

}
