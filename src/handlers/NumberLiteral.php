<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Context;

class NumberLiteral extends SimpleHandler {

	const NODE_NEEDS_TEXT = true;

	public static function handle(array $node, Context $context) {
		return new NumberValue($node['number']);
	}

	public static function reduce(array &$node): void {

		$value = str_replace('_', '', $node['text']);
		$float = (float) $value;
		if ($float >= \PHP_INT_MAX || $float <= \PHP_INT_MIN) {
			throw new ErrorException("Number overflow ({$value}).", $node);
		}

		// As string.
		$node['number'] = $value;
		unset($node['text']);

	}

}
