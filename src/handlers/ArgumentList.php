<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Helpers\SimpleHandler;

/**
 * Node fields:
 * function: Function name.
 * args: List of arguments.
 * body: Node representing contents of code to execute as a function..
 */
class ArgumentList extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		if (!isset($node['args'])) {
			return [];
		}

		$list = [];
		foreach ($node['args'] as $a) {
			$handler = HandlerFactory::get($a['name']);
			$list[] = $handler::handle($a, $context);
		}

		return $list;

	}

	public static function reduce(array &$node): void {

		// Make sure this is always list, even with one item.
		if (isset($node['args'])) {
			$node['args'] = Common::ensureIndexed($node['args']);
		}

	}

}
