<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Handlers\ChainedHandler;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Structures\InsertionProxy;

/**
 * This handler returns a final part of the chain - a value object that's
 * derived from the vector and which supports insertion. All values but the last
 * part of the chain also must support dereferencing.
 */
class Vector extends ChainedHandler {

	public static function chain(
		array $node,
		Context $context,
		AbstractValue $subject
	) {

		$handler = HandlerFactory::getFor($node['index']['name']);
		$key = $handler::run($node['index'], $context, $subject);

		// Are we going to handle this node as a leaf node?
		if (!isset($node['vector'])) {
			// If this is a leaf node, return an insertion proxy.
			return new InsertionProxy($key, $subject);
		}

		// This is not a leaf node, so just dereference the chain a bit deeper,
		// so we can ultimately end up with some leaf node. (that situation
		// will be handled by the code above).
		$next = $subject->itemGet($key);
		if ($next === \null) {
			throw new RuntimeError(\sprintf(
				"Type '%s' does not support item access", $subject::TYPE
			));
		}

		// At this point we know there's some another, deeper part of vector,
		// so process it.
		$handler = HandlerFactory::getFor($node['vector']['name']);
		return $handler::chain($node['vector'], $context, $next);

	}

}
