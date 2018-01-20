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
class MethodCall extends \Smuuf\Primi\StrictObject implements IChainedHandler {

	public static function chain(array $node, Context $context, \Smuuf\Primi\Structures\Value $subject) {

		$methodName = $node['method']['text'];

		// Evaluate and prepare a list of arguments for later.
		$argList = [];
		if (isset($node['args'])) {
			$handler = HandlerFactory::get($node['args']['name']);
			$argList = $handler::handle($node['args'], $context);
		}

		$valueMethod = \sprintf("call%s", \ucfirst($methodName));
		if (!\method_exists($subject, $valueMethod)) {
			throw new ErrorException(sprintf(
				"Calling undefined method '%s' on value '%s'.",
				$valueMethod,
				$subject::TYPE
			), $node);
		}

		try {
			return $subject->$valueMethod(...$argList);
		} catch (\TypeError $e) {

			// Make use of PHP's internal TypeError being thrown when passing wrong types of arguments.
			throw new ErrorException(sprintf(
				"Wrong arguments passed to method '%s' of value '%s'.",
				$methodName,
				$subject::TYPE
			), $node);

		}

	}

}
