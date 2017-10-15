<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class Operand extends \Smuuf\Primi\Object implements IHandler, IReducer {

	public static function handle(array $node, Context $context, \Smuuf\Primi\Structures\Value $chain = null) {

		$handler = HandlerFactory::get($node['core']['name']);

		// Pass in the chained value, if it was given.
		// This way we don't break up the chain when chaining methods / values.
		$value = $handler::handle($node['core'], $context, $chain);

		if (isset($node['next'])) {

			$handler = HandlerFactory::get($node['next']['name']);
			// This is the "next" node and we are thus chaining - pass the
			// value so far to the currently assigned handler.
			$value = $handler::handle($node['next'], $context, $value);

		}

		return $value;

	}

	public static function reduce(array $node) {

		// If this node has a value method call with it, don't reduce it.
		if (isset($node['method']) || isset($node['next'])) {
			return;
		}

		// Otherwise reduce it to just the core node.
		return $node['core'];

	}

}
