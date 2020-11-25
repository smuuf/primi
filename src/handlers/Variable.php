<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\UndefinedVariableError;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Structures\Value;

class Variable extends SimpleHandler {

	protected static function handle(array $node, Context $context) {
		return self::fetch($node['core']['text'], $context);
	}

	public static function fetch(
		string $name,
		Context $context
	): Value {

		$value = $context->getVariable($name);
		if ($value === \null) {
			throw new UndefinedVariableError($name);
		}

		return $value;

	}

}
