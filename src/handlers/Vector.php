<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\UndefinedIndexException;
use \Smuuf\Primi\InternalUndefinedIndexException;

use \Smuuf\Primi\ISupportsArrayAccess;

use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers;

class Vector extends \Smuuf\Primi\StrictObject implements IChainedHandler {

	/**
	 * This handler returns a final part of the chain - a value object that's derived from the vector and which
	 * supports insertion. All values but the last part of the chain also must support dereferencing.
	 */
	public static function chain(array $node, Context $context, \Smuuf\Primi\Structures\Value $subject) {

		if (isset($node['arrayKey'])) {
			$handler = HandlerFactory::get($node['arrayKey']['name']);
			$key = $handler::handle($node['arrayKey'], $context, $subject);
			$key = $key->getInternalValue();
			$type = 'array';
		}

		if (isset($node['propKey'])) {
			$handler = HandlerFactory::get($node['propKey']['name']);
			$key = $handler::handle($node['propKey'], $context, $subject);
			$type = 'prop';
		}

		// Are we going to handle this node as a leaf node?
		$isLeaf = !isset($node['vector']);

		if ($isLeaf) {

			// If this is a leaf node, return an insertion proxy.
			// Whether it is an array or property insertion proxy is determined
			// by the fact whether this node was a part of an array or property
			// vector.

			if ($type === 'array') {
				return $subject->getArrayInsertionProxy($key);
			}

			if ($type === 'prop') {
				return $subject->getPropertyInsertionProxy($key);
			}

		}

		// This is not a leaf node, so just dereference the chain a bit deeper,
		// so we can ultimately end up with some leaf node. (that situation
		// will be handled by the code above).

		if ($type === 'array') {
			$next = $subject->arrayGet($key);
		}

		if ($type === 'prop') {
			$next = $subject->propertyGet($key);
		}

		// At this point we know there's some another, deeper part of vector,
		// so process it.
		$handler = HandlerFactory::get($node['vector']['name']);
		return $handler::chain($node['vector'], $context, $next);

	}

}
