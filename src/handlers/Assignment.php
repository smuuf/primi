<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\LookupError;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Structures\InsertionProxy;

class Assignment extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		// Execute the right-hand node first.
		$rightHandler = HandlerFactory::get($node['right']['name']);
		$return = $rightHandler::handle($node['right'], $context);

		$leftHandler = HandlerFactory::get($node['left']['name']);
		$target = $leftHandler::handle($node['left'], $context);

		switch (\true) {
			case \is_string($target):
				// Store the return value into variable.
				$context->setVariable($node['left']['text'], $return);
			break;
			case $target instanceof InsertionProxy:

				// Vector handler returns a proxy with the key being
				// pre-configured. Commit the value to that key into the correct
				// value object.
				try {
					$target->commit($return);
				} catch (LookupError|TypeError $e) {
					throw new RuntimeError($e->getMessage(), $node);
				}

			break;
		}

		// An assignment also returns its value.
		return $return;

	}

}
