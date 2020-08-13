<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\BreakException;
use \Smuuf\Primi\Helpers\SimpleHandler;

class BreakStatement extends SimpleHandler {

	public static function handle(array $node, Context $context) {
		throw new BreakException;
	}

}
