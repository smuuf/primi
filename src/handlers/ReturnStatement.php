<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\ReturnException;
use \Smuuf\Primi\Helpers\SimpleHandler;

class ReturnStatement extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		if (!isset($node['subject'])) {
			throw new ReturnException(\null);
		}

		$handler = HandlerFactory::get($node['subject']['name']);
		throw new ReturnException($handler::handle($node['subject'], $context));

	}

}
