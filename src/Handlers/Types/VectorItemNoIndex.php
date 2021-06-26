<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Handlers\ChainedHandler;
use \Smuuf\Primi\Structures\ItemInsertionProxy;

class VectorItemNoIndex extends ChainedHandler {

	public static function chain(
		array $node,
		Context $context,
		AbstractValue $subject
	) {

		// This can only be a leaf node. Key is null, since it is not specified.
		return new ItemInsertionProxy(null, $subject);

	}

}
