<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\InternalUndefinedTruthnessException;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\ContinueException;
use \Smuuf\Primi\BreakException;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class WhileStatement extends \Smuuf\Primi\StrictObject implements IHandler {

	public static function handle(array $node, Context $context) {

		// Execute the left-hand node and get its return value.
		$condHandler = HandlerFactory::get($node['left']['name']);
		$blockHandler = HandlerFactory::get($node['right']['name']);

		try {

			while (
				Common::isTruthy($condHandler::handle($node['left'], $context))
			) {
				try {
					$blockHandler::handle($node['right'], $context);
				} catch (ContinueException $e) {
					continue;
				} catch (BreakException $e) {
					break;
				}
			}

		} catch (InternalUndefinedTruthnessException $e) {
			throw new ErrorException($e->getMessage(), $node);
		}

	}

}
