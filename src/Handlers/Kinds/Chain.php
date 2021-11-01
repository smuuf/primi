<?php

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Handlers\ChainedHandler;
use \Smuuf\Primi\Handlers\HandlerFactory;

class Chain extends ChainedHandler {

	public static function chain(
		array $node,
		Context $context,
		AbstractValue $subject
	) {

		// Handle the item; pass in the origin subject.
		$handler = HandlerFactory::getFor($node['core']['name']);
		$value = $handler::chain($node['core'], $context, $subject);

		// If there's chain, handle it, too.
		if (isset($node['chain'])) {
			$handler = HandlerFactory::getFor($node['chain']['name']);
			return $handler::chain($node['chain'], $context, $value);
		}

		return $value;

	}

}
