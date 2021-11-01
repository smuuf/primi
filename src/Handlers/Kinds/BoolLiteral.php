<?php

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Handlers\SimpleHandler;

class BoolLiteral extends SimpleHandler {

	const NODE_NEEDS_TEXT = \true;

	protected static function handle(array $node, Context $context) {
		return Interned::bool($node['text'] === "true");
	}

}
