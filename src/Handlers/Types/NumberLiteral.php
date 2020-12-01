<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Values\NumberValue;

class NumberLiteral extends SimpleHandler {

	const NODE_NEEDS_TEXT = \true;

	protected static function handle(array $node, Context $context) {
		return new NumberValue($node['number']);
	}

	public static function reduce(array &$node): void {

		// As string.
		$node['number'] = \str_replace('_', '', $node['text']);
		unset($node['text']);

	}

}
