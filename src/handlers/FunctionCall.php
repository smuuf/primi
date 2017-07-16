<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

/**
 * Node fields:
 * function: Function name.
 * args: List of arguments.
 * body: Node representing contents of code to execute as a function..
 */
class FunctionCall extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {

		$functionName = $node['function']['text'];

		// Handle situation with solo arguments (which wouldn't be represented as array).
		// Do it by placing solo arguments into arrays.
		if (isset($node['args']['name'])) {
			$node['args'] = [$node['args']];
		}

		$argumentList = [];
		if (isset($node['args'])) {
			foreach ($node['args'] as $a) {
				$handler = HandlerFactory::get($a['name']);
				$argumentList[] = $handler::handle($a, $context);
			}
		}

		if (empty($function = $context->getFunction($functionName))) {
			throw new \Smuuf\Primi\ErrorException("Function '$functionName' is not defined", $node);
		}

		return $function->call($argumentList, $context);

	}

}
