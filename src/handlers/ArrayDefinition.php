<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\ArrayValue;
use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers;

class ArrayDefinition extends \Smuuf\Primi\StrictObject implements IHandler {

	public static function handle(array $node, Context $context) {

		if (!isset($node['items'])) {
			return new ArrayValue([]);
		}

		$result = [];
		$indexCounter = 0;

		Helpers::ensureIndexed($node['items']);

		foreach ($node['items'] as $itemNode) {

			// Key doesn't have to be defined.
			if (isset($itemNode['key'])) {

				// But if it is defined for this item, use it.
				$keyHandler = HandlerFactory::get($itemNode['key']['name']);
				$key = $keyHandler::handle($itemNode['key'], $context)->getPhpValue();

				// And if it is a numeric integer, use it as a base for the index counter
				// we would have used if the key was not provided.
				if (NumberValue::isNumericInt($key)) {
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

		return new ArrayValue($result);

	}

}
