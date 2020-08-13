<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Ex\BreakException;
use \Smuuf\Primi\Ex\ContinueException;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Helpers\SimpleHandler;

class WhileStatement extends SimpleHandler {

	public static function handle(
		array $node,
		Context $context
	) {

		// Execute the left-hand node and get its return value.
		$condHandler = HandlerFactory::get($node['left']['name']);
		$blockHandler = HandlerFactory::get($node['right']['name']);

		while (
			$condHandler::handle($node['left'], $context)->isTruthy()
		) {

			try {
				$blockHandler::handle($node['right'], $context);
			} catch (ContinueException $e) {
				continue;
			} catch (BreakException $e) {
				break;
			}

		}

	}

}
