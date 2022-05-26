<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Handlers\SimpleHandler;

class Operand extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		// Handle the item; pass in the chained value, if it was given.
		$value = HandlerFactory::runNode($node['core'], $context);

		// If there's chain, handle it.
		if (isset($node['chain'])) {
			$handler = HandlerFactory::getFor($node['chain']['name']);
			return $handler::chain($node['chain'], $context, $value);
		}

		return $value;

	}

	public static function reduce(array &$node): void {

		// If this node has a value method call with it, don't reduce it.
		if (!isset($node['chain'])) {
			$node = $node['core'];
		}

	}

}
