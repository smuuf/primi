<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Helpers\ChainedHandler;
use \Smuuf\Primi\Structures\Value;

class Invocation extends ChainedHandler {

	public static function chain(
		array $node,
		Context $context,
		Value $fn
	) {

		try {

			$arguments = [];
			if (isset($node['args'])) {
				$handler = HandlerFactory::get($node['args']['name']);
				$arguments = $handler::handle($node['args'], $context);
			}

			// If the node contains an argument to be prepended to the arg list,
			// do exactly that. (This is used then for chained functions.)
			$prepend = $node['prepend_arg'] ?? \null;
			if ($prepend) {
				\array_unshift($arguments, $prepend);
			}

			$result = $fn->invoke($arguments);
			if ($result === null) {
				throw new RuntimeError(
					\sprintf("Trying to invoke a non-function '%s'", $fn::TYPE),
					$node
				);
			}

			return $result;

		} catch (RuntimeError $e) {
			throw new RuntimeError($e->getMessage(), $node);
		}

	}

}
