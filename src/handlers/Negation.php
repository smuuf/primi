<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Structures\BoolValue;

use function \Smuuf\Primi\Helpers\ensure_indexed as primifn_ensure_indexed;

class Negation extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		$handler = HandlerFactory::get($node['core']['name']);
		$truthness = $handler::handle($node['core'], $context)->isTruthy();

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
			$node['nots'] = primifn_ensure_indexed($node['nots']);
		}

	}

}
