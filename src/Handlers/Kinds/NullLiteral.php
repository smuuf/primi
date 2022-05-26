<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Handlers\SimpleHandler;

class NullLiteral extends SimpleHandler {

	protected static function handle(array $node, Context $context) {
		return Interned::null();
	}

}
