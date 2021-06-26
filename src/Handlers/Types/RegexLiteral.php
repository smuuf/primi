<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Values\RegexValue;

class RegexLiteral extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		// The core node's text is already prepared by StringLiteral - it is
		// already stripped of the quotes around the literal, so we can use it
		// directly.
		return new RegexValue($node['core']['text']);

	}

}
