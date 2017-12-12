<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\InternalUndefinedFunctionException;
use \Smuuf\Primi\Context;

/**
 * Node fields:
 * function: Function name.
 * args: List of arguments.
 */
class FunctionCall extends \Smuuf\Primi\StrictObject implements IHandler {

	public static function handle(array $node, Context $context) {

		$functionName = $node['function']['text'];

		$argList = [];
		if (isset($node['args'])) {
			$handler = HandlerFactory::get($node['args']['name']);
			$argList = $handler::handle($node['args'], $context);
		}

		try {
			$function = $context->getFunction($functionName);
		} catch (InternalUndefinedFunctionException $e) {
			throw new ErrorException("Calling undefined function '$functionName'.", $node);
		}

		return $function->call($argList, $context, $node);

	}

}
