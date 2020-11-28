<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Handlers\SimpleHandler;

class BoolLiteral extends SimpleHandler {

	const NODE_NEEDS_TEXT = \true;

	protected static function handle(array $node, Context $context) {
		return new BoolValue($node['text'] === "true");
	}

}
