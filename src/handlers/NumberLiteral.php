<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\Context;

class NumberLiteral extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {
		return new NumberValue($node['text']);
	}

}
