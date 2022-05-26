<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\ContinueException;
use \Smuuf\Primi\Handlers\SimpleHandler;

class ContinueStatement extends SimpleHandler {

	protected static function handle(array $node, Context $context) {
		throw new ContinueException;
	}

}
