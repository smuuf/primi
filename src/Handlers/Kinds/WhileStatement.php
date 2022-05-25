<?php

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Ex\BreakException;
use \Smuuf\Primi\Ex\ContinueException;
use \Smuuf\Primi\Handlers\SimpleHandler;

class WhileStatement extends SimpleHandler {

	protected static function handle(
		array $node,
		Context $context
	) {

		// Execute the left-hand node and get its return value.
		$condHandler = HandlerFactory::getFor($node['left']['name']);
		$blockHandler = HandlerFactory::getFor($node['right']['name']);

		// Counter for determining when to tick the task queue.
		$tickCounter = 0;
		$queue = $context->getTaskQueue();

		while (
			$condHandler::run($node['left'], $context)->isTruthy()
		) {

			// Tick the task queue every 4 iterations.
			if (++$tickCounter === 4) {
				$context->getTaskQueue()->tick();
				$queue->tick();
				$tickCounter = 0;
			}

			try {

				$blockHandler::run($node['right'], $context);
				if ($context->hasRetval()) {
					return;
				}

			} catch (ContinueException $_) {
				continue;
			} catch (BreakException $_) {
				break;
			}

		}

		$queue->tick();

	}

}
