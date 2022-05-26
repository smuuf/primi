<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\BreakException;
use \Smuuf\Primi\Handlers\SimpleHandler;

class BreakStatement extends SimpleHandler {

	protected static function handle(array $node, Context $context) {
		throw new BreakException;
	}

}
