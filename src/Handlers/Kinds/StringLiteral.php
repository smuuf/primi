<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Helpers\StringEscaping;
use \Smuuf\Primi\Handlers\SimpleHandler;

class StringLiteral extends SimpleHandler {

	const NODE_NEEDS_TEXT = \true;

	protected static function handle(array $node, Context $context) {
		return Interned::string($node['text']);
	}

	public static function reduce(array &$node): void {

		$node['text'] = StringEscaping::unescapeString($node['core']['text']);

		unset($node['quote']);
		unset($node['core']);

	}

}
