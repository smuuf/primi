<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

/**
 * Node fields:
 * left: A comparison expression node.
 * right: Node representing contents of code to execute while left-hand result is truthy.
 */
class WhileStatement extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {

		// Execute the left-hand node and get its return value.
		$conditionHandler = HandlerFactory::get($node['left']['name']);
		$blockHandler = HandlerFactory::get($node['right']['name']);

		while ($conditionHandler::handle($node['left'], $context)->getPhpValue()) {
			$blockHandler::handle($node['right'], $context);
		}

	}

}
