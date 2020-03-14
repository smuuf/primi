<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\UndefinedIndexException;
use \Smuuf\Primi\Structures\InsertionProxy;
use \Smuuf\Primi\InternalUndefinedIndexException;


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
				} catch (InternalUndefinedIndexException $e) {
					throw new UndefinedIndexException($e->getMessage(), $node);
				} catch (\TypeError $e) {
					throw new ErrorException(sprintf(
						"Cannot insert '%s' into '%s'",
						$return::TYPE,
						$target->getTarget()::TYPE
					));
				}

			break;
		}

		// An assignment also returns its value.
		return $return;

	}

}
