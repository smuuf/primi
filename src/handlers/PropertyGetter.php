<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Context;

/**
 * Node fields:
 * method: Method name.
 * args: List of arguments.
 */
class PropertyGetter extends \Smuuf\Primi\StrictObject implements IChainedHandler {

	public static function chain(array $node, Context $context, \Smuuf\Primi\Structures\Value $subject) {

		$handler = HandlerFactory::get($node['core']['name']);
		$propName = $handler::handle($node['core'], $context);

		$methodName = \sprintf("prop%s", \ucfirst($propName));
		if (!\method_exists($subject, $methodName)) {
			throw new ErrorException(sprintf(
				"Undefined property '%s' of value '%s'.",
				$propName,
				$subject::TYPE
			), $node);
		}

		return $subject->$methodName();

	}

}
