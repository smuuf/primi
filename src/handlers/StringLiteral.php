<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Helpers\StringEscaping;
use \Smuuf\Primi\Structures\StringValue;

class StringLiteral extends SimpleHandler {

	const NODE_NEEDS_TEXT = true;

	public static function handle(array $node, Context $context) {

		$content = $node['text'];

		// Trim quotes from the start and the end using substr().
		// Using trim("\"'", ...) would make "abc'" into abc instead of abc',
		// so do this a little more directly.
		$value = \mb_substr($content, 1, \mb_strlen($content) - 2);
		$value = StringEscaping::unescapeString($value);

		return new StringValue($value);

	}

}
