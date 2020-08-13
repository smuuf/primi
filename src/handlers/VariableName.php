<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\SimpleHandler;

class VariableName extends SimpleHandler {

	const NODE_NEEDS_TEXT = \true;

	public static function handle(array $node, Context $context) {
		return $node['text'];
	}

}
