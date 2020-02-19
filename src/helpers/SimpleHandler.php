<?php

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\StrictObject;
use \Smuuf\Primi\Structures\Value;

/**
 * Common ancestor of LogicalAnd and LogicalOr handlers, both of which have
 * the exact same implementation, but are separated on a grammar level for
 * operators "and" and "or" to have a distinct precedences.
 *
 * Using the third optional parameter signalizes usage of a chained handler.
 * Chained handlers have the ability to pass an additional Value structure into
 * nested handler calls.
 */
abstract class SimpleHandler extends BaseHandler {

	abstract public static function handle(
		array $node,
		Context $context
	);

}
