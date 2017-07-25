<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class Operand extends \Smuuf\Primi\Object implements IHandler, IReducer {

	public static function handle(array $node, Context $context) {

		$handler = HandlerFactory::get($node['core']['name']);
		$value = $handler::handle($node['core'], $context);

		if (isset($node['method'])) {

			if (!isset($node['method'][0])) {
				$node['method'] = [$node['method']];
			}

			foreach ($node['method'] as $single) {
				$handler = HandlerFactory::get($single['name']);
				$value = $handler::handle($single, $context, $value);
			}

		}

		return $value;

	}

	public static function reduce(array $node) {

		// If this node has a value method call with it, don't reduce it.
		if (isset($node['method'])) {
			return;
		}

		// Otherwise reduce it to just the core node.
		return $node['core'];

	}

}
