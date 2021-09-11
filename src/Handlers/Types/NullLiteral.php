<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Handlers\SimpleHandler;

class NullLiteral extends SimpleHandler {

	protected static function handle(array $node, Context $context) {
		return Interned::null();
	}

}
