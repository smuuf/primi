<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\RegexValue;
use \Smuuf\Primi\Context;

class RegexLiteral extends \Smuuf\Primi\StrictObject implements IHandler {

	public static function handle(array $node, Context $context) {
		return new RegexValue($node['text']);
	}

}
