<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Context;

class BoolLiteral extends \Smuuf\Primi\StrictObject implements IHandler {

	public static function handle(array $node, Context $context) {
		return new BoolValue($node['text'] === "true");
	}

}
