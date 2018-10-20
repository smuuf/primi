<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class Negation extends \Smuuf\Primi\StrictObject implements IHandler, IReducer {

	public static function handle(array $node, Context $context) {

		$handler = HandlerFactory::get($node['core']['name']);
		$value = $handler::handle($node['core'], $context);

		// The final truthness is (at least for now) based on PHP's own rules.
		$truthness = (bool) Common::isTruthy($value);

		// Should we even handle negation? If there's an even number of negation
		// operators, the result would always have the same truthness as its
		// input.
		Common::ensureIndexed($node['nots']);
		$isNegation = count($node['nots'] ?? []) % 2;

		return new BoolValue($isNegation ? !$truthness : $truthness);

	}

	public static function reduce(array $node) {

		// If this truly has a negation, do not reduce this node.
		// If not, return only core.
		if (!isset($node['nots'])) {
			return $node['core'];
		}

	}

}
