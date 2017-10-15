<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\ReturnException;

class ReturnStatement extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {

		$returnValue = \null;

		if (isset($node['subject'])) {
			$handler = HandlerFactory::get($node['subject']['name']);
			$returnValue = $handler::handle($node['subject'], $context);
		}

		throw new ReturnException($returnValue);

	}

}
