<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Values\BoolValue;

class Negation extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		$handler = HandlerFactory::getFor($node['core']['name']);
		$truthness = $handler::run($node['core'], $context)->isTruthy();

		// Should we even handle negation? If there's an even number of negation
		// operators, the result would always have the same truthness as its
		// input.
		$isNegation = \count($node['nots'] ?? []) % 2;

		return new BoolValue($isNegation ? !$truthness : $truthness);

	}

	public static function reduce(array &$node): void {

		// If this truly has a negation, do not reduce this node.
		// If not, return only core.
		if (!isset($node['nots'])) {
			$node = $node['core'];
		} else {
			$node['nots'] = Func::ensure_indexed($node['nots']);
		}

	}

}
