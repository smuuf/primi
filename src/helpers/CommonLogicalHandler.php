<?php

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Helpers\LogicalLTR;
use \Smuuf\Primi\Helpers\SimpleHandler;

/**
 * Common ancestor of LogicalAnd and LogicalOr handlers, both of which have
 * the exact same implementation, but are separated on a grammar level for
 * operators "and" and "or" to have a distinct precedences.
 */
abstract class CommonLogicalHandler extends SimpleHandler {

	public static function handle(array $node, Context $context) {
		return LogicalLTR::handle($node, $context);
	}

	public static function reduce(array &$node): void {

		if (!isset($node['ops'])) {
			$node = $node['operands'];
		} else {
			$node['ops'] = Common::ensureIndexed($node['ops']);
		}

	}

}
