<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class Chain extends \Smuuf\Primi\StrictObject implements IChainedHandler {

	public static function chain(array $node, Context $context, \Smuuf\Primi\Structures\Value $subject) {

		// Handle the item; pass in the origin subject.
		$handler = HandlerFactory::get($node['core']['name']);
		$value = $handler::chain($node['core'], $context, $subject);

		// If there's chain, handle it, too.
		if (isset($node['chain'])) {
			$handler = HandlerFactory::get($node['chain']['name']);
			return $handler::chain($node['chain'], $context, $value);
		}

		return $value;

	}

}
