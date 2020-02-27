<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\InternalUndefinedTruthnessException;

class CondExpr extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		// Execute the left-hand node and get its return value.
		$condHandler = HandlerFactory::get($node['cond']['name']);
		$return = $condHandler::handle($node['cond'], $context);

		try {

			// If the result of the left hand equals to truthy value,
			// execute the code branch stored in the right-hand node.
			if (Common::isTruthy($return)) {
				$trueHandler = HandlerFactory::get($node['true']['name']);
				return $trueHandler::handle($node['true'], $context);
			} else {
				$falseHandler = HandlerFactory::get($node['false']['name']);
				return $falseHandler::handle($node['false'], $context);
			}

		} catch (InternalUndefinedTruthnessException $e) {
			throw new ErrorException($e->getMessage(), $node);
		}

	}

	public static function reduce(array &$node): void {

		if (!isset($node['cond'])) {
			$node = $node['true'];
		}

	}

}
