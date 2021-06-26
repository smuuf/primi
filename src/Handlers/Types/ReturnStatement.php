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

		$handler = HandlerFactory::getFor($node['subject']['name']);
		throw new ReturnException($handler::run($node['subject'], $context));

	}

}
