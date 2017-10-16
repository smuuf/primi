<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\UndefinedVariableException;
use \Smuuf\Primi\InternalUndefinedVariableException;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class Variable extends \Smuuf\Primi\StrictObject implements IHandler {

	public static function handle(array $node, Context $context) {

		if (isset($node['pre']) || isset($node['post'])) {
			return UnaryOperator::handle($node, $context);
		}

		$variableName = HandlerFactory
			::get($node['core']['name'])
			::handle($node['core'], $context);

		try {
			return $context->getVariable($variableName);
		} catch (InternalUndefinedVariableException $e) {
			throw new UndefinedVariableException($e->getMessage(), $node);
		}

	}

}
