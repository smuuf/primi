<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;

class NumberLiteral extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {

		$content = $node['text'];
		return self::isNumericInt($content) ? (int) $content : (float) $content;

	}

	protected static function isNumericInt($input) {
		return (string) (int) $input === (string) $input;
	}

}