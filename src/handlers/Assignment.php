<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\InsertionProxy;

use \Smuuf\Primi\ISupportsInsertion;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class Assignment extends \Smuuf\Primi\Object implements IHandler {

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
				// Vector handler returns a proxy with the key being pre-configured.
				// Commit the value to that key into the correct value object.
				$target->commit($return);
			break;
		}

		// Assignment also returns its value.
		return $return;

	}

}
