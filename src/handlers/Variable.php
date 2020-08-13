<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\UndefinedVariableError;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Structures\Value;

class Variable extends SimpleHandler {

	public static function handle(array $node, Context $context) {
		return self::fetch($node['core']['text'], $node, $context);
	}

	public static function fetch(
		string $name,
		array $node,
		Context $context
	): Value {

		$value = $context->getVariable($name);
		if ($value === null) {
			throw new UndefinedVariableError($name, $node);
		}

		return $value;

	}

}
