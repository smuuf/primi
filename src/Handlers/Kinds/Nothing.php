<?php

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\SimpleHandler;

class Nothing extends SimpleHandler {

	protected static function handle(array $node, Context $context) {
		return \null;
	}

}
