<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Ex\ReturnException;
use \Smuuf\Primi\Helpers\SimpleHandler;

class ReturnStatement extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		if (!isset($node['subject'])) {
			throw new ReturnException;
		}

		$handler = HandlerFactory::get($node['subject']['name']);
		throw new ReturnException($handler::run($node['subject'], $context));

	}

}
