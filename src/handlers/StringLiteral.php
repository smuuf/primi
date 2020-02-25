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
		return new StringValue(StringEscaping::unescapeString($node['text']));
	}

	public static function reduce(array &$node): void {

		// Trim quotes from the start and the end using substr().
		// Using trim("\"'", ...) would make "abc'" into abc instead of abc',
		// so do this a little more directly.
		$node['text'] = \mb_substr(
			$node['text'], 1, \mb_strlen($node['text']) - 2
		);

	}

}
