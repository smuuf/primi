<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Helpers\SimpleHandler;

/**
 * Node fields:
 * left: A comparison expression node.
 * right: Node representing contents of code to execute if left-hand result is truthy.
 */
class IfStatement extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		// Execute the left-hand node and get its return value.
		$leftHandler = HandlerFactory::get($node['left']['name']);
		$return = $leftHandler::handle($node['left'], $context);

		// If the result of the left hand equals to truthy value,
		// execute the code branch stored in the right-hand node.
		if ($return->isTruthy()) {
			$rightHandler = HandlerFactory::get($node['right']['name']);
			$rightHandler::handle($node['right'], $context);
		} elseif (isset($node['else'])) {
			$elseHandler = HandlerFactory::get($node['else']['name']);
			$elseHandler::handle($node['else'], $context);
		}

	}

}
