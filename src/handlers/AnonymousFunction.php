<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\FuncValue;
use \Smuuf\Primi\Structures\FunctionContainer;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers;

/**
 * Node fields:
 * function: Function name.
 * args: List of arguments.
 * body: Node representing contents of code to execute as a function..
 */
class AnonymousFunction extends \Smuuf\Primi\StrictObject implements IHandler {

	public static function handle(array $node, Context $context) {

		$argumentList = [];
		if (isset($node['args'])) {

			Helpers::ensureIndexed($node['args']);
			foreach ($node['args'] as $a) {
				$argumentList[] = $a['text'];
			}

		}

		// Build convenient hash to ease identification amongst other functions.
		$name = substr(Helpers::hash($argumentList, $node['body']), 0, 16);

		$fn = FunctionContainer::build($node['body'], $argumentList, $context);
		return new FuncValue($fn);

	}

}
