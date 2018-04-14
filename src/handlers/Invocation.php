<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\FuncValue;
use \Smuuf\Primi\Helpers;
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

			return $fn->invoke($arguments);

		} catch (\ArgumentCountError | InternalArgumentCountException $e) {

			if ($e instanceof \InternalArgumentCountException) {

				[$expected, $passed] = [$e->getExpectedCount(), $e->getPassedCount()];

			} else {

				// We have the counts of expected/passed arguments available,
				// add that information to the error message.
				[$expected, $passed] = Helpers::parseArgumentCountError($e);

				// Also, because of how calling Primi value methods work, we need to
				// subtract 1 from these numbers. (first argument is the value - upon
				// which the method is called - itself).
				$expected--;
				$passed--;

			}

			$bound = $fn->getBoundValue();

			$details = null;
			if ($expected !== null && $passed !== null) {
				$details = sprintf(" (%d instead of %d)", $passed, $expected);
			}

			$type = null;
			if ($bound) {
				$type = sprintf(" of type '%s'", $bound::TYPE);
			}

			$msg = sprintf("Too few arguments passed to function%s%s", $type, $details);
			throw new ErrorException($msg, $node);

		} catch (\TypeError $e) {

			$bound = $fn->getBoundValue();
			$type = null;
			if ($bound) {
				$type = sprintf(" of type '%s'", $bound::TYPE);
			}

			// Make use of PHP's internal TypeError being thrown when passing wrong types of arguments.
			throw new ErrorException(sprintf("Wrong arguments passed to function%s", $type), $node);


		}

	}

}
