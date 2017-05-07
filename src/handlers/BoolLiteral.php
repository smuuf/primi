<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;

class BoolLiteral extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {

		if ($node['text'] === "true") {
			return true;
		} else {
			return false;
		}

	}

}