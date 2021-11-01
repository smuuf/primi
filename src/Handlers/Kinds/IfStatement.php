<?php

namespace Smuuf\Primi\Handlers\Kinds;

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
		$result = HandlerFactory::runNode($node['left'], $context);

		// If the result of the left hand equals to truthy value,
		// execute the code branch stored in the right-hand node.
		if ($result->isTruthy()) {
			HandlerFactory::runNode($node['right'], $context);
		} elseif (isset($node['else'])) {
			HandlerFactory::runNode($node['else'], $context);
		}

	}

}
