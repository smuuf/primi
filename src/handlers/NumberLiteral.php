<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Context;

class NumberLiteral extends SimpleHandler {

	const NODE_NEEDS_TEXT = true;

	public static function handle(array $node, Context $context) {

		$value = $node['text'];

		$int = $value;
		if ($int >= \PHP_INT_MAX || $int <= \PHP_INT_MIN) {
			throw new ErrorException("Number overflow ({$value}).", $node);
		}

		return new NumberValue($value);

	}

}
