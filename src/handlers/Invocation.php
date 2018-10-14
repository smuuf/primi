<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\FuncValue;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\InternalArgumentCountException;
use \Smuuf\Primi\InternalUndefinedVariableException;
use \Smuuf\Primi\InternalException;
use \Smuuf\Primi\Context;

class Invocation extends \Smuuf\Primi\StrictObject implements IChainedHandler {

	public static function chain(array $node, Context $context, Value $fn) {

		if (!$fn instanceof FuncValue) {
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

			$msg = self::buildArgumentCountErrorMessage($e, $fn);
			throw new ErrorException($msg, $node);

		} catch (\TypeError $e) {

			$msg = "Wrong type of argument passed to function";
			throw new ErrorException($msg, $node);

		}

	}

	private static function buildArgumentCountErrorMessage(
		\Throwable $e,
		FuncValue $fn
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
				"Cannot parse argument count from unexpected exception."
			);

		}

		$details = null;
		if ($expected !== null && $passed !== null) {
			$details = sprintf(" (%d instead of %d)", $passed, $expected);
		}

		return sprintf("Too few arguments passed to function%s", $details);

	}

}
