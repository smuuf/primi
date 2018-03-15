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
		if ($name = $node['variable']['text'] ?? false) {
			try {
				$function = $context->getVariable($name);
			} catch (InternalUndefinedVariableException $e) {
				throw new ErrorException("Calling undefined function '$name'.", $node);
			}
		} elseif ($valueNode = $node['value'] ?? false) {
			$handler = HandlerFactory::get($valueNode['name']);
			$function = $handler::handle($valueNode, $context);
		} else {
			throw new InternalException("Bad reference to a function.");
		}

		// Prevent calling a non-function value.
		if (!$function instanceof \Smuuf\Primi\Structures\FuncValue) {
			throw new ErrorException(
				sprintf("Trying to call a non-function '%s'.", $function::TYPE),
				$node
			);
		}

		$arguments = [];
		if (isset($node['args'])) {
			$handler = HandlerFactory::get($node['args']['name']);
			$arguments = $handler::handle($node['args'], $context);
		}

		return $function->invoke($arguments, $context, $node);

	}

}
