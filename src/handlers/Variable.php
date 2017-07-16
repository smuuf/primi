<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\ISupportsDereference;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class Variable extends \Smuuf\Primi\Object implements IHandler, IReducer {

	public static function handle(array $node, Context $context) {

		if ($unaryResult = UnaryOperator::handle($node, $context)) {
			return $unaryResult;
		}

		$handler = HandlerFactory::get($node['core']['name']);
		$value = $handler::handle($node['core'], $context);

		if (isset($node['dereference'])) {

			// If there's only one "dereference", we want to represent it the same way as if there were more of them,
			// so we can both process them the same way - via foreach.
			if (isset($node['dereference']['name'])) {
				$node['dereference'] = [$node['dereference']];
			}

			foreach ($node['dereference'] as $keyNode) {

				if (!$value instanceof ISupportsDereference) {
					throw new \Smuuf\Primi\ErrorException("Value does not support dereference key", $node);
				}

				$handler = HandlerFactory::get($keyNode['name']);
				$key = $handler::handle($keyNode, $context);
				$value = $value->dereference($key);

			}

		}

		return $value;

	}

	public static function reduce(array $node) {

		// If this node has any unary operator or dereference, don't reduce it.
		if (isset($node['post']) || isset($node['pre']) || isset($node['dereference'])) {
			return;
		}

		// Otherwise reduce it to just the core node.
		return $node['core'];

	}

}
