<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Handlers\SimpleHandler;

class CondExpr extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		// Execute the left-hand node and get its return value.
		$condHandler = HandlerFactory::getFor($node['cond']['name']);
		$return = $condHandler::run($node['cond'], $context);

		// If the result of the left hand equals to truthy value,
		// execute the code branch stored in the right-hand node.
		if ($return->isTruthy()) {
			$trueHandler = HandlerFactory::getFor($node['true']['name']);
			return $trueHandler::run($node['true'], $context);
		} else {
			$falseHandler = HandlerFactory::getFor($node['false']['name']);
			return $falseHandler::run($node['false'], $context);
		}

	}

	public static function reduce(array &$node): void {

		if (!isset($node['cond'])) {
			$node = $node['true'];
		}

	}

}
