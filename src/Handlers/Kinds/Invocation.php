<?php

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Location;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Handlers\ChainedHandler;
use \Smuuf\Primi\Handlers\HandlerFactory;

class Invocation extends ChainedHandler {

	public static function chain(
		array $node,
		Context $context,
		AbstractValue $fn
	) {

		$arguments = \null;
		if (isset($node['args'])) {
			$arguments = HandlerFactory::runNode($node['args'], $context);
		}

		// Gather info about call location - for some quality tracebacks.
		$callsite = new Location(
			$context->getCurrentModule()->getStringRepr(),
			$node['_l'],
			$node['_p']
		);

		return $fn->invoke($context, $arguments, $callsite);

	}

}
