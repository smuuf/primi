<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Handlers\ChainedHandler;
use \Smuuf\Primi\Structures\ItemInsertionProxy;

class VectorItem extends ChainedHandler {

	public static function chain(
		array $node,
		Context $context,
		AbstractValue $subject
	) {

		$handler = HandlerFactory::getFor($node['index']['name']);
		$key = $handler::run($node['index'], $context, $subject);

		// If this is a leaf node, return an insertion proxy.
		if ($node['leaf']) {
			return new ItemInsertionProxy($key, $subject);
		}

		// This is not a leaf node, so just return the value this non-leaf node
		// points to.
		$value = $subject->itemGet($key);
		if ($value === \null) {
			throw new RuntimeError(\sprintf(
				"Type '%s' does not support item access", $subject::TYPE
			));
		}

		return $value;

	}

}
