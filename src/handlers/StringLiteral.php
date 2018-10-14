<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\StringValue;
use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\Context;

class StringLiteral extends \Smuuf\Primi\StrictObject implements IHandler {

	public static function handle(array $node, Context $context) {

		$content = $node['text'];

		// Trim quotes from the start and the end using substr().
		// Using trim("\"'", ...) would make "abc'" into abc instead of abc',
		// so do this a little more directly.
		$value = \mb_substr($content, 1, \mb_strlen($content) - 2);

		return new StringValue($value);

	}

}
