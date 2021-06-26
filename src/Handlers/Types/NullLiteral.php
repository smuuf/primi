<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Values\NullValue;

class NullLiteral extends SimpleHandler {

	protected static function handle(array $node, Context $context) {
		return NullValue::build();
	}

}
