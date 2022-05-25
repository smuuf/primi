<?php

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\BreakException;
use \Smuuf\Primi\Ex\ContinueException;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Structures\AssignmentTargets;

/**
 * Node fields:
 * left: A source iterator.
 * item: Variable name to store the single item in.
 * right: Node representing contents of code to execute while iterating the iterator structure.
 */
class ForStatement extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		// Execute the left-hand node and get its return value.
		$subject = HandlerFactory::runNode($node['left'], $context);

		$iter = $subject->getIterator();
		if ($iter === \null) {
			throw new RuntimeError(
				\sprintf("Cannot iterate over '%s'", $subject->getTypeName())
			);
		}

		/** @var AssignmentTargets */
		$targets = HandlerFactory::runNode($node['targets'], $context);
		$blockHandler = HandlerFactory::getFor($node['right']['name']);

		// 1-bit value for ticking task queue once per two iterations.
		$tickBit = 0;
		$queue = $context->getTaskQueue();

		foreach ($iter as $i) {

			// Switch the bit from 1/0 or vice versa.
			if ($tickBit ^= 1) {
				$queue->tick();
			}

			$targets->assign($i, $context);

			try {

				$blockHandler::run($node['right'], $context);
				if ($context->hasRetval()) {
					return;
				}

			} catch (ContinueException $e) {
				continue;
			} catch (BreakException $e) {
				break;
			}

		}

		$context->getTaskQueue()->tick();

	}

}
