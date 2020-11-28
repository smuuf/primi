<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\UndefinedVariableError;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Values\AbstractValue;

class Variable extends SimpleHandler {

	protected static function handle(array $node, Context $context) {
		return self::fetch($node['core']['text'], $context);
	}

	public static function fetch(
		string $name,
		Context $context
	): AbstractValue {

		$value = $context->getVariable($name);
		if ($value === \null) {
			throw new UndefinedVariableError($name);
		}

		return $value;

	}

}
