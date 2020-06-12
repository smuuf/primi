<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Helpers\SimpleHandler;

class VariableVector extends SimpleHandler {

	/**
	 * This handler returns a final part of the chain - a value object that's
	 * derived from the vector and which supports insertion. All values but the
	 * last part of the chain also must support key access.
	 */
	public static function handle(array $node, Context $context) {

		// Handle the item; pass in the origin subject.
		$handler = HandlerFactory::get($node['core']['name']);
		$value = $handler::handle($node['core'], $context);

		// There's chain, so handle it, too.
		$handler = HandlerFactory::get($node['vector']['name']);
		return $handler::chain($node['vector'], $context, $value);

	}

}
