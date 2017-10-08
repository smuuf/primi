<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\Context;

class NumberLiteral extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {

		$value = $node['text'];

		$int = $value;
		if ($int >= PHP_INT_MAX || $int <= PHP_INT_MIN) {
			throw new \Smuuf\Primi\ErrorException("Number overflow ({$value}).", $node);
		}

		return new NumberValue($value);

	}

}
