<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Context;

/**
 * Node fields:
 * function: Function name.
 * args: List of arguments.
 * body: Node representing contents of code to execute as a function..
 */
class FunctionCall extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context, \Smuuf\Primi\Structures\Value $value = null) {

		$functionName = $node['function']['text'];

		$argumentList = [];
		if (isset($node['args'])) {

			// Handle situation with solo arguments (which wouldn't be represented as array).
			// Do it by placing solo arguments into arrays.
			if (!isset($node['args'][0])) {
				$node['args'] = [$node['args']];
			}

			foreach ($node['args'] as $a) {
				$handler = HandlerFactory::get($a['name']);
				$argumentList[] = $handler::handle($a, $context);
			}

		}

		// If a value object is provided, try to call the function upon it (we're calling the object's method).
		if ($value) {

			$methodName = sprintf("call%s", ucfirst($functionName));
			if (!method_exists($value, $methodName)) {
				throw new ErrorException(sprintf(
					"Calling unsupported method '%s' on value type '%s'.",
					$functionName,
					$value::TYPE
				), $node);
			}

			try {
				return $value->$methodName(...$argumentList);
			} catch (\TypeError $e) {

				// Make use of PHP's internal TypeError being thrown when passing wrong types of arguments.
				throw new ErrorException(sprintf(
					"Wrong type of argument passed to method '%s' of value '%s'.",
					$functionName,
					$value::TYPE
				), $node);

			}

		} else {

			// Standard function call.
			if (empty($function = $context->getFunction($functionName))) {
				throw new ErrorException("Calling '$functionName' undefined function.", $node);
			}

			return $function->call($argumentList, $context, $node);

		}

	}

}
