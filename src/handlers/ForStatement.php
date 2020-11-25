<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\BreakException;
use \Smuuf\Primi\Ex\ContinueException;
/**
 * Node fields:
 * left: A source iterator.
 * item: Variable name to store the single item in.
 * right: Node representing contents of code to execute while iterating the iterator structure.
 */
class ForStatement extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		// Execute the left-hand node and get its return value.
		$leftHandler = HandlerFactory::get($node['left']['name']);
		$subject = $leftHandler::run($node['left'], $context);

		$iter = $subject->getIterator();
		if ($iter === null) {
			throw new RuntimeError(
				\sprintf("Cannot iterate over '%s'", $subject::TYPE)
			);
		}

		$keyVariableName = $node['key']['text'] ?? false;
		$itemVariableName = $node['item']['text'];
		$blockHandler = HandlerFactory::get($node['right']['name']);

		foreach ($iter as $k => $i) {

			if ($keyVariableName) {
				$context->setVariable($keyVariableName, $k);
			}

			$context->setVariable($itemVariableName, $i);

			try {
				$blockHandler::run($node['right'], $context);
			} catch (ContinueException $e) {
				continue;
			} catch (BreakException $e) {
				break;
			}

		}

	}

}
