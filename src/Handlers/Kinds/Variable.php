<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\UndefinedVariableError;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Values\AbstractValue;

class Variable extends SimpleHandler {

	protected static function handle(array $node, Context $context) {
		return self::fetch($node['var'], $context);
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

	public static function reduce(array &$node): void {
		$node['var'] = $node['core']['text'];
		unset($node['core']);
	}

}
