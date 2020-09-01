<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Structures\NumberValue;

class NumberLiteral extends SimpleHandler {

	const NODE_NEEDS_TEXT = \true;

	public static function handle(array $node, Context $context) {
		return new NumberValue($node['number']);
	}

	public static function reduce(array &$node): void {

		// As string.
		$node['number'] = $value = \str_replace('_', '', $node['text']);
		unset($node['text']);

	}

}
