<?php

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\StrictObject;

/**
 * Common ancestor of LogicalAnd and LogicalOr handlers, both of which have
 * the exact same implementation, but are separated on a grammar level for
 * operators "and" and "or" to have a distinct precedences.
 *
 * Using the third optional parameter signalizes usage of a chained handler.
 * Chained handlers have the ability to pass an additional Value structure into
 * nested handler calls.
 */
abstract class BaseHandler extends StrictObject {

	const NODE_NEEDS_TEXT = false;

	public static function reduce(array $node): ?array {
		return null;
	}

}
