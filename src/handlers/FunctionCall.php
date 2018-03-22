<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\InternalUndefinedVariableException;
use \Smuuf\Primi\InternalException;
use \Smuuf\Primi\Context;

/**
 * Node fields:
 * function: Function name.
 * args: List of arguments.
 */
class FunctionCall extends \Smuuf\Primi\StrictObject implements IHandler {

	public static function handle(array $node, Context $context) {

		// Function can be referenced by:
		// a) its name (fn stored in variable or defined globally (still technically a variable),
		// b) its value (a "function value" directly).

		if (isset($node['variable']['text'])) {

			$name = $node['variable']['text'];

			try {
				$fn = $context->getVariable($name);
			} catch (InternalUndefinedVariableException $e) {
				throw new ErrorException("Calling undefined function '$name'", $node);
			}

		} elseif (isset($node['value'])) {

			$handler = HandlerFactory::get($node['value']['name']);
			$fn = $handler::handle($node['value'], $context);

		} else {
			throw new InternalException("Bad reference to a function.");
		}

		// Prevent calling a non-function value.
		if (!$fn instanceof \Smuuf\Primi\Structures\FuncValue) {
			throw new ErrorException(sprintf("Trying to call a non-function '%s'.", $fn::TYPE), $node);
		}

		$arguments = [];
		if (isset($node['args'])) {
			$handler = HandlerFactory::get($node['args']['name']);
			$arguments = $handler::handle($node['args'], $context);
		}

		return $fn->invoke($arguments, $context, $node);

	}

}
