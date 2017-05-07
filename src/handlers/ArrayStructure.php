<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class ArrayStructure extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {

		if (!isset($node['items'])) {
			return [];
		}

		$result = [];
		$indexCounter = 0;
		foreach ($node['items'] as $itemNode) {

			// Key doesn't have to be defined.
			if (isset($itemNode['key'])) {

				// But if it is defined for this item, use it.
				$keyHandler = HandlerFactory::get($itemNode['key']['name']);
				$key = $keyHandler::handle($itemNode['key'], $context);

				// And if it is a numeric integer, use it as a base for the index counter
				// we would have used if the key was not provided.
				if (self::isNumericInt($key)) {
					$indexCounter = $key + 1;
				}

			} else {

				// The key was not provided, assign a key for this item using our internal index counter.
				$key = $indexCounter++;

			}

			$valueHandler = HandlerFactory::get($itemNode['value']['name']);
			$value = $valueHandler::handle($itemNode['value'], $context);

			$result[$key] = $value;

		}

		return $result;

	}

	protected static function isNumericInt($input) {
		return (string) (int) $input === (string) $input;
	}

}