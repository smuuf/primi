<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Helpers\SimpleHandler;

class CondExpr extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		// Execute the left-hand node and get its return value.
		$condHandler = HandlerFactory::get($node['cond']['name']);
		$return = $condHandler::handle($node['cond'], $context);

		// If the result of the left hand equals to truthy value,
		// execute the code branch stored in the right-hand node.
		if ($return->isTruthy()) {
			$trueHandler = HandlerFactory::get($node['true']['name']);
			return $trueHandler::handle($node['true'], $context);
		} else {
			$falseHandler = HandlerFactory::get($node['false']['name']);
			return $falseHandler::handle($node['false'], $context);
		}

	}

	public static function reduce(array &$node): void {

		if (!isset($node['cond'])) {
			$node = $node['true'];
		}

	}

}
