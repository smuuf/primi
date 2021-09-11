<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Ex\ReturnException;
use \Smuuf\Primi\Handlers\SimpleHandler;

class ReturnStatement extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		if (!isset($node['subject'])) {
			throw new ReturnException;
		}

		throw new ReturnException(
			HandlerFactory::runNode($node['subject'], $context)
		);

	}

}
