<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\SimpleHandler;

class Nothing extends SimpleHandler {

	protected static function handle(array $node, Context $context) {
		return null;
	}

}
