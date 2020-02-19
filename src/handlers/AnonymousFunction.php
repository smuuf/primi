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
class AnonymousFunction extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		$argumentList = [];
		if (isset($node['args'])) {

			Common::ensureIndexed($node['args']);
			foreach ($node['args'] as $a) {
				$argumentList[] = $a['text'];
			}

		}

		$fn = FnContainer::build($node['body'], $argumentList, $context);
		return new FuncValue($fn);

	}

}
