<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Context;

class NumberLiteral extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {
		return Value::build(Value::TYPE_NUMBER, $node['text']);
	}

}
