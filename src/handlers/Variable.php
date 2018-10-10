<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\InternalUndefinedVariableException;
use \Smuuf\Primi\UndefinedVariableException;

class Variable extends \Smuuf\Primi\StrictObject implements IHandler {

	public static function handle(array $node, Context $context) {

		$variableName = HandlerFactory
			::get($node['core']['name'])
			::handle($node['core'], $context);

		return self::fetch($variableName, $node, $context);

	}

	public static function fetch(
		string $name,
		array $node,
		Context $context
	): Value {

		try {
			return $context->getVariable($name);
		} catch (InternalUndefinedVariableException $e) {
			throw new UndefinedVariableException($e->getMessage(), $node);
		}

	}

}
