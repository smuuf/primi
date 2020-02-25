<?php

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Helpers\LogicalLTR;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\InternalUndefinedTruthnessException;

/**
 * Common ancestor of LogicalAnd and LogicalOr handlers, both of which have
 * the exact same implementation, but are separated on a grammar level for
 * operators "and" and "or" to have a distinct precedences.
 */
abstract class CommonLogicalHandler extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		try {
			return LogicalLTR::handle($node, $context);
		} catch (InternalUndefinedTruthnessException $e) {
			throw new ErrorException($e->getMessage(), $node);
		}

	}

	public static function reduce(array &$node): void {

		if (!isset($node['ops'])) {
			$node = $node['operands'];
		} else {
			$node['ops'] = Common::ensureIndexed($node['ops']);
		}

	}

}
