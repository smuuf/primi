<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Structures\NullValue;

class Program extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		foreach ($node['nodes'] as $sub) {
			$handler = HandlerFactory::get($sub['name']);
			$returnValue = $handler::handle($sub, $context);
		}

		return $returnValue ?? new NullValue;

	}

}
