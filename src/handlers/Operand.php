<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Helpers\SimpleHandler;

class Operand extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		// Handle the item; pass in the chained value, if it was given.
		$handler = HandlerFactory::get($node['core']['name']);
		$value = $handler::handle($node['core'], $context);

		// If there's chain, handle it.
		if (isset($node['chain'])) {
			$handler = HandlerFactory::get($node['chain']['name']);
			return $handler::chain($node['chain'], $context, $value);
		}

		return $value;

	}

	public static function reduce(array $node): ?array {

		// If this node has a value method call with it, don't reduce it.
		if (isset($node['chain'])) {
			return null;
		}

		// Otherwise reduce it to just the core node.
		return $node['core'];

	}

}
