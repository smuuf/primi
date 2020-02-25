<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\UndefinedVariableException;

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
			throw new UndefinedVariableException($name, $node);
		}

		return $value;

	}

}
