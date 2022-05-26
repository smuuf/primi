<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\EngineInternalError;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Structures\AssignmentTargets;
use \Smuuf\Primi\Structures\InsertionProxyInterface;

class Assignment extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		// Execute the right-hand node first.
		$return = HandlerFactory::runNode($node['right'], $context);
		$target = HandlerFactory::runNode($node['left'], $context);

		if (\is_string($target)) {
			// Store the return value into variable in current scope.
			$context->setVariable($target, $return);
			return $return;
		}

		if ($target instanceof InsertionProxyInterface) {
			// Vector handler returns a proxy with the key being
			// pre-configured. Commit the value to that key into the correct
			// value object.
			$target->commit($return);
			return $return;
		}

		if ($target instanceof AssignmentTargets) {
			$target->assign($return, $context);
			return $return;
		}

		throw new EngineInternalError("Invalid assignment target");

	}

}
