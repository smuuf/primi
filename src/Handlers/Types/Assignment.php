<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Structures\InsertionProxy;

class Assignment extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		// Execute the right-hand node first.
		$rightHandler = HandlerFactory::getFor($node['right']['name']);
		$return = $rightHandler::run($node['right'], $context);

		$leftHandler = HandlerFactory::getFor($node['left']['name']);
		$target = $leftHandler::run($node['left'], $context);

		switch (\true) {
			case \is_string($target):

				// Store the return value into variable in current scope.
				$context->setVariable(
					$node['left']['text'],
					$return
				);

			break;
			case $target instanceof InsertionProxy:

				// Vector handler returns a proxy with the key being
				// pre-configured. Commit the value to that key into the correct
				// value object.
				$target->commit($return);

			break;
		}

		// An assignment also returns its value.
		return $return;

	}

}
