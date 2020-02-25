<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Structures\RegexValue;

class RegexLiteral extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		// The core node's text is already prepared by StringLiteral - it is
		// already stripped of the quotes around the literal, so we can use it
		// directly.
		return new RegexValue($node['core']['text']);

	}

}
