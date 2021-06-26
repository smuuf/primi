<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Handlers\SimpleHandler;

/**
 * Node fields:
 * left: A comparison expression node.
 * right: Node representing contents of code to execute if left-hand result is truthy.
 */
class IfStatement extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		// Execute the left-hand node and get its return value.
		$leftHandler = HandlerFactory::getFor($node['left']['name']);
		$return = $leftHandler::run($node['left'], $context);

		// If the result of the left hand equals to truthy value,
		// execute the code branch stored in the right-hand node.
		if ($return->isTruthy()) {
			$rightHandler = HandlerFactory::getFor($node['right']['name']);
			$rightHandler::run($node['right'], $context);
		} elseif (isset($node['else'])) {
			$elseHandler = HandlerFactory::getFor($node['else']['name']);
			$elseHandler::run($node['else'], $context);
		}

	}

}
