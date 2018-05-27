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

			return $fn->invoke($arguments);

		} catch (\ArgumentCountError | InternalArgumentCountException $e) {

			$msg = self::buildArgumentCountErrorMessage($e, $fn);
			throw new ErrorException($msg, $node);

		} catch (\TypeError $e) {

			$msg = self::buildTypeErrorMessage($e, $fn);
			throw new ErrorException($msg, $node);

		}

	}

	private static function buildTypeErrorMessage(
		\Throwable $e,
		FuncValue $fn
	): string {

		$type = null;
		if ($bound = $fn->getBoundValue()) {
			$type = sprintf(" of type '%s'", $bound::TYPE);
		}

		// Make use of PHP's internal TypeError being thrown when passing wrong
		// types of arguments.
		return sprintf("Wrong arguments passed to function%s", $type);

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
			[$expected, $passed] = Common::parseArgumentCountError($e);

			// Also, because of how calling Primi value methods work, we need to
			// subtract 1 from these numbers. (first argument is the value - upon
			// which the method is called - itself).
			$expected--;
			$passed--;

		} else {

			throw new InternalException(
				"Cannot parse argument count from unexpected exception."
			);

		}

		$details = null;
		if ($expected !== null && $passed !== null) {
			$details = sprintf(" (%d instead of %d)", $passed, $expected);
		}

		$type = null;
		if ($bound = $fn->getBoundValue()) {
			$type = sprintf(" of type '%s'", $bound::TYPE);
		}

		return sprintf("Too few arguments passed to function%s%s", $type, $details);

	}

}
