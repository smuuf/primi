<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

/**
 * Node fields:
 * left: A source iterator.
 * item: Variable name to store the single item in.
 * right: Node representing contents of code to execute while iterating the iterator structure.
 */
class ForeachStatement extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {

		// Execute the left-hand node and get its return value.
		$leftHandler = HandlerFactory::get($node['left']['name']);
		$subject = $leftHandler::handle($node['left'], $context);

		if (!is_array($subject) && !is_string($subject)) {
			throw new \Smuuf\Primi\ErrorException("Cannot iterate over '{$node['left']['text']}' variable which is not an array");
		}

		$elementVariableName = $node['item']['text'];
		$blockHandler = HandlerFactory::get($node['right']['name']);

		if (is_string($subject)) {
			$subject = self::utfSplit($subject);
		}

		foreach ($subject as $i) {
			$context->setVariable($elementVariableName, $i);
			$blockHandler::handle($node['right'], $context);
		}

	}

	private static function utfSplit($str = '', $len = 1) {
		preg_match_all("/./u", $str, $arr);
		$arr = array_chunk($arr[0], $len);
		$arr = array_map('implode', $arr);
		return $arr;
	}

}