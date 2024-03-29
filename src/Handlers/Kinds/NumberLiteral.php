<?php

declare(strict_types=1);

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Handlers\SimpleHandler;

class NumberLiteral extends SimpleHandler {

	const NODE_NEEDS_TEXT = \true;

	protected static function handle(array $node, Context $context) {
		return Interned::number($node['number']);
	}

	public static function reduce(array &$node): void {

		// As string.
		$node['number'] = \str_replace('_', '', $node['text']);
		unset($node['text']);

	}

}
