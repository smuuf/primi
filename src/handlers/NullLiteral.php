<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Structures\NullValue;

class NullLiteral extends SimpleHandler {

	protected static function handle(array $node, Context $context) {
		return new NullValue;
	}

}
