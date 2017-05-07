<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class Literal extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {

		$handler = HandlerFactory::get($node['name']);
		return $handler::handle($node, $context);

	}

}