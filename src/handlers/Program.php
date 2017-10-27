<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class Program extends \Smuuf\Primi\StrictObject implements IHandler {

	public static function handle(array $node, Context $context) {

		foreach ($node['nodes'] as $sub) {
			$handler = HandlerFactory::get($sub['name']);
			$returnValue = $handler::handle($sub, $context);
		}

		return $returnValue;

	}

}
