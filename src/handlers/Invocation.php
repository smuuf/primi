<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\InternalException;
use \Smuuf\Primi\ISupportsInvocation;
use \Smuuf\Primi\Helpers\ChainedHandler;
use \Smuuf\Primi\InternalArgumentCountException;

class Invocation extends ChainedHandler {

	public static function chain(
		array $node,
		Context $context,
		Value $fn
	) {

		if (!$fn instanceof ISupportsInvocation) {
			throw new ErrorException(
				sprintf("Trying to invoke a non-function '%s'", $fn::TYPE),
				$node
			);
		}

		$arguments = [];
		if (isset($node['args'])) {
			$handler = HandlerFactory::get($node['args']['name']);
			$arguments = $handler::handle($node['args'], $context);
		}

		try {

			// If the node contains an argument to be prepended to the arg list,
			// do exactly that. (This is used then for chained functions.)
			$prepend = $node['prepend_arg'] ?? null;
			if ($prepend) {
				\array_unshift($arguments, $prepend);
			}

			return $fn->invoke($arguments);

		} catch (\ArgumentCountError | InternalArgumentCountException $e) {

			$msg = self::buildArgumentCountErrorMessage($e);
			throw new ErrorException($msg, $node);

		} catch (\TypeError $e) {

			$msg = "Wrong type of argument passed to function";
			throw new ErrorException($msg, $node);

		}

	}

	private static function buildArgumentCountErrorMessage(
		\Throwable $e
	): string {

		if ($e instanceof InternalArgumentCountException) {

			[$expected, $passed] = [
				$e->getExpectedCount(),
				$e->getPassedCount()
			];

		} elseif ($e instanceof \ArgumentCountError) {

			// We have the counts of expected/passed arguments available,
			// add that information to the error message.
			[$passed, $expected] = Common::parseArgumentCountError($e);

		} else {

			throw new InternalException(
				"Cannot parse argument count from unexpected exception"
			);

		}

		return sprintf(
			"Function expects %d argument%s (got %d)",
			$expected,
			(int) $expected === 1 ? '' : 's',
			$passed
		);

	}

}
