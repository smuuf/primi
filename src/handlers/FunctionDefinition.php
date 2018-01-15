<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers;

/**
 * Node fields:
 * function: Function name.
 * args: List of arguments.
 * body: Node representing contents of code to execute as a function..
 */
class FunctionDefinition extends \Smuuf\Primi\StrictObject implements IHandler {

	public static function handle(array $node, Context $context) {

		$functionName = $node['function']['text'];

		$argumentList = [];
		if (isset($node['args'])) {

			Helpers::ensureIndexed($node['args']);

			foreach ($node['args'] as $a) {
				$argumentList[] = $a['text'];
			}

		}

		$context->setVariable(
			$functionName,
			new \Smuuf\Primi\Structures\FuncValue(
				$functionName,
				$argumentList,
				$node['body']
			)
		);

	}

}
