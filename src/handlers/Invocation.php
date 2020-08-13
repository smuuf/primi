<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\ISupportsInvocation;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Helpers\ChainedHandler;

class Invocation extends ChainedHandler {

	public static function chain(
		array $node,
		Context $context,
		Value $fn
	) {

		try {

			if (!$fn instanceof ISupportsInvocation) {
				throw new RuntimeError(
					\sprintf("Trying to invoke a non-function '%s'", $fn::TYPE),
					$node
				);
			}

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

			return $fn->invoke($arguments);

		} catch (RuntimeError $e) {
			throw new RuntimeError($e->getMessage(), $node);
		}

	}

}
