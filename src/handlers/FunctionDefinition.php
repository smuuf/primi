<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Structures\FuncValue;
use \Smuuf\Primi\Structures\FnContainer;

/**
 * Node fields:
 * function: Function name.
 * args: List of arguments.
 * body: Node representing contents of code to execute as a function..
 */
class FunctionDefinition extends SimpleHandler {

	const NODE_NEEDS_TEXT = true;

	public static function handle(array $node, Context $context) {

		$functionName = $node['function']['text'];
		$argumentList = [];

		if (isset($node['args'])) {

			Common::ensureIndexed($node['args']);
			foreach ($node['args'] as $a) {
				$argumentList[] = $a['text'];
			}

		}

		$fnc = FnContainer::build($node['body'], $argumentList, $context);
		$context->setVariable($functionName, new FuncValue($fnc));

	}

}
