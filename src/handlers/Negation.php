<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\InternalUndefinedTruthnessException;

class Negation extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		$handler = HandlerFactory::get($node['core']['name']);
		$value = $handler::handle($node['core'], $context);

		try {

			// The final truthness is (at least for now) based on PHP's own
			// rules.
			$truthness = Common::isTruthy($value);

		} catch (InternalUndefinedTruthnessException $e) {
			throw new ErrorException($e->getMessage(), $node);
		}

		// Should we even handle negation? If there's an even number of negation
		// operators, the result would always have the same truthness as its
		// input.
		$isNegation = count($node['nots'] ?? []) % 2;

		return new BoolValue($isNegation ? !$truthness : $truthness);

	}

	public static function reduce(array &$node): void {

		// If this truly has a negation, do not reduce this node.
		// If not, return only core.
		if (!isset($node['nots'])) {
			$node = $node['core'];
		} else {
			$node['nots'] = Common::ensureIndexed($node['nots']);
		}

	}

}
