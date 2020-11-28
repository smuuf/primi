<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\ContinueException;
use \Smuuf\Primi\Handlers\SimpleHandler;

class ContinueStatement extends SimpleHandler {

	protected static function handle(array $node, Context $context) {
		throw new ContinueException;
	}

}
