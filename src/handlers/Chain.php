<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class Chain extends \Smuuf\Primi\StrictObject implements IHandler {

	public static function handle(array $node, Context $context, \Smuuf\Primi\Structures\Value $chain = null) {

		// Handle the item; pass in the chained value, if it was given.
		$handler = HandlerFactory::get($node['core']['name']);
		$value = $handler::handle($node['core'], $context, $chain);

		if (isset($node['chain'])) {
			$handler = HandlerFactory::get($node['chain']['name']);
			$value = $handler::handle($node['chain'], $context, $value);
		}

		return $value;

	}

}
