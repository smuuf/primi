<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\SimpleHandler;

class Block extends SimpleHandler {

	public static function handle(array $node, Context $context) {
		// This handler shouldn't even be needed, if it had an inside node.
		// This method is here just to fulfill the handler abstact function.
	}

}
