<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Helpers\SimpleHandler;

class BoolLiteral extends SimpleHandler {

	const NODE_NEEDS_TEXT = \true;

	protected static function handle(array $node, Context $context) {
		return new BoolValue($node['text'] === "true");
	}

}
