<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

/**
 * Node fields:
 * left: A "+" or "-" sign signalling the 'side' of the first operand.
 * right: List of operand nodes.
 */
class Comparison extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {

		// Execute the left-hand node and get its return value.
		$leftHandler = HandlerFactory::get($node['left']['name']);
		$leftReturn = $leftHandler::handle($node['left'], $context);

		$rightHandler = HandlerFactory::get($node['right']['name']);
		$rightReturn = $rightHandler::handle($node['right'], $context);

		switch ($node['op']['text']) {
			case "==":
				return new BoolValue($leftReturn == $rightReturn);
			case "!=":
				return new BoolValue($leftReturn != $rightReturn);
			case ">":
				return new BoolValue($leftReturn > $rightReturn);
			case "<":
				return new BoolValue($leftReturn < $rightReturn);
			case ">=":
				return new BoolValue($leftReturn >= $rightReturn);
			case "<=":
				return new BoolValue($leftReturn <= $rightReturn);
		}

	}

}
