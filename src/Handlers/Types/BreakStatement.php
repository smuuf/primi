<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\BreakException;
use \Smuuf\Primi\Handlers\SimpleHandler;

class BreakStatement extends SimpleHandler {

	protected static function handle(array $node, Context $context) {
		throw new BreakException;
	}

}
