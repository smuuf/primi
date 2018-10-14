<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\ISupportsArrayAccess;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

/**
 * This handler returns a final part of the chain - a value object that's
 * derived from the vector and which supports insertion. All values but the last
 * part of the chain also must support dereferencing.
 */
class Vector extends \Smuuf\Primi\StrictObject implements IChainedHandler {

	public static function chain(
		array $node,
		Context $context,
		Value $subject
	) {

		if (!$subject instanceof ISupportsArrayAccess) {
			throw new ErrorException(sprintf(
				"Cannot insert into '%s'",
				$subject::TYPE
			), $node);
		}

		$key = \null;

		$handler = HandlerFactory::get($node['arrayKey']['name']);
		$key = $handler::handle($node['arrayKey'], $context, $subject);
		$key = $key->getInternalValue();

		// Are we going to handle this node as a leaf node?
		$isLeaf = !isset($node['vector']);

		// If this is a leaf node, return an insertion proxy.
		if ($isLeaf) {
			return $subject->getArrayInsertionProxy($key);
		}

		// This is not a leaf node, so just dereference the chain a bit deeper,
		// so we can ultimately end up with some leaf node. (that situation
		// will be handled by the code above).

		$next = $subject->arrayGet($key);

		// At this point we know there's some another, deeper part of vector,
		// so process it.
		$handler = HandlerFactory::get($node['vector']['name']);
		return $handler::chain($node['vector'], $context, $next);

	}

}
