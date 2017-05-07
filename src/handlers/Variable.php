<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class Variable extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {

		$handler = HandlerFactory::get($node['core']['name']);
		$value = $handler::handle($node['core'], $context);

		if (UnaryOperator::handle($node, $context)) {
			return UnaryOperator::getReturnValue($node, $value);
		};

		if (isset($node['dereference'])) {

			// If there's only one "dereference", we want to represent
			// it the same way as if there were more of them,
			// so we can both process them the same way - via foreach.
			if (isset($node['dereference']['name'])) {
				$node['dereference'] = [$node['dereference']];
			}

			foreach ($node['dereference'] as $keyNode) {
				$handler = HandlerFactory::get($keyNode['name']);
				$key = $handler::handle($keyNode, $context);

				if (!isset($value[$key])) {
					throw new \Smuuf\Primi\ErrorException("Undefined key '$key'");
				}

				$value = $value[$key];

			}

		};

		return $value;

	}

}