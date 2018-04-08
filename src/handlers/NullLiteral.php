<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\NullValue;
use \Smuuf\Primi\Context;

class NullLiteral extends \Smuuf\Primi\StrictObject implements IHandler {

	public static function handle(array $node, Context $context) {
		return new NullValue;
	}

}
