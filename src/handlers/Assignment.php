<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class Assignment extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {

		// Execute the right-hand node and get its return value.
		$rightHandler = HandlerFactory::get($node['right']['name']);
		$return = $rightHandler::handle($node['right'], $context);

		// Store the return value into variable.
		$context->setVariable($node['left']['text'], $return);

		// Assignment also returns its value.
		return $return;

	}

}
