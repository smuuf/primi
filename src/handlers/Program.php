<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Structures\NullValue;

class Program extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		foreach ($node['stmts'] as $sub) {
			$handler = HandlerFactory::get($sub['name']);
			$returnValue = $handler::handle($sub, $context);
		}

		return $returnValue ?? new NullValue;

	}

	public static function reduce(array &$node): void {

		// Make sure the list of statements has proper form.
		if (isset($node['stmts'])) {
			$node['stmts'] = Common::ensureIndexed($node['stmts']);
		} else {
			// ... even if there are no statements at all.
			$node['stmts'] = [];
		}

	}

}
