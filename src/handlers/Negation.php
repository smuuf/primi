<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class Negation extends \Smuuf\Primi\StrictObject implements IHandler, IReducer {

	public static function handle(array $node, Context $context) {

		$negation = isset($node['not']);

		$handler = HandlerFactory::get($node['core']['name']);
		$value = $handler::handle($node['core'], $context);

		// Should we even handle negation?
		if ($negation) {

			// If so, let's determine truthness using PHP's own rules (at least
			// for now).
			$truthness = (bool) $value->getInternalValue();
			$value = new BoolValue(!$truthness);

		}

		return $value;

	}

	public static function reduce(array $node) {

		// If this truly has a negation, do not reduce this node.
		if (isset($node['not'])) {
			return;
		}

		// Otherwise reduce it to just the core node.
		return $node['core'];

	}

}
