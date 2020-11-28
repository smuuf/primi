<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Ex\ContextAwareException;
use \Smuuf\Primi\Handlers\SimpleHandler;

class TryStatement extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		try {

			// Execute the main code.
			$mainHandler = HandlerFactory::getFor($node['main']['name']);
			return $mainHandler::run($node['main'], $context);

		} catch (ContextAwareException $e) {

			// Execute the onerror block if any error occurred with the main
			// code.
			$errorHandler = HandlerFactory::getFor($node['onerror']['name']);
			return $errorHandler::run($node['onerror'], $context);

		}

	}

}
