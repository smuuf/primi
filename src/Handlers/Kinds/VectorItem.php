<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

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

		$key = HandlerFactory::runNode($node['index'], $context);

		// If this is a leaf node, return an insertion proxy.
		if ($node['leaf']) {
			return new ItemInsertionProxy($key, $subject);
		}

		// This is not a leaf node, so just return the value this non-leaf node
		// points to.
		$value = $subject->itemGet($key);
		if ($value === \null) {
			throw new RuntimeError(\sprintf(
				"Type '%s' does not support item access",
				$subject->getTypeName()
			));
		}

		return $value;

	}

}
