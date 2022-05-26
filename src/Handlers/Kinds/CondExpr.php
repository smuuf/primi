<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Handlers\SimpleHandler;

class CondExpr extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		// Execute the left-hand node and get its return value.
		$return = HandlerFactory::runNode($node['cond'], $context);

		// If the result of the left hand equals to truthy value,
		// execute the code branch stored in the right-hand node.
		if ($return->isTruthy()) {
			return HandlerFactory::runNode($node['true'], $context);
		} else {
			return HandlerFactory::runNode($node['false'], $context);
		}

	}

	public static function reduce(array &$node): void {

		if (!isset($node['cond'])) {
			$node = $node['true'];
		}

	}

}
