<?php

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\ErrorException;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Handlers\SimpleHandler;

class TryStatement extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		try {

			// Execute the main code.
			return HandlerFactory::runNode($node['main'], $context);

		} catch (ErrorException $e) {

			// Execute the onerror block if any error occurred with the main code.
			return HandlerFactory::runNode($node['onerror'], $context);

		}

	}

}
