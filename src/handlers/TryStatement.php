<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Helpers\SimpleHandler;

class TryStatement extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		try {

			// Execute the main code.
			$mainHandler = HandlerFactory::get($node['main']['name']);
			return $mainHandler::handle($node['main'], $context);

		} catch (ErrorException $e) {

			// Execute the onerror block if any error occured with the main
			// code.
			$errorHandler = HandlerFactory::get($node['onerror']['name']);
			return $errorHandler::handle($node['onerror'], $context);

		}

	}

}
