<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\Common;

/**
 * Node fields:
 * function: Function name.
 * args: List of arguments.
 * body: Node representing contents of code to execute as a function..
 */
class ArgumentList extends \Smuuf\Primi\StrictObject implements IHandler {

	public static function handle(array $node, Context $context) {

		if (!isset($node['args'])) {
			return [];
		}

		$list = [];
		Common::ensureIndexed($node['args']);

		foreach ($node['args'] as $a) {
			$handler = HandlerFactory::get($a['name']);
			$list[] = $handler::handle($a, $context);
		}

		return $list;

	}

}
