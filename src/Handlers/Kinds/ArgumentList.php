<?php

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Handlers\HandlerFactory;

/**
 * Node fields:
 * function: Function name.
 * args: List of arguments.
 * body: Node representing contents of code to execute as a function..
 */
class ArgumentList extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		if (!isset($node['args'])) {
			return [];
		}

		$list = [];
		foreach ($node['args'] as $arg) {
			$list[] = HandlerFactory::runNode($arg, $context);
		}

		return $list;

	}

	public static function reduce(array &$node): void {

		// Make sure this is always list, even with one item.
		if (isset($node['args'])) {
			$node['args'] = Func::ensure_indexed($node['args']);
		}

	}

}
