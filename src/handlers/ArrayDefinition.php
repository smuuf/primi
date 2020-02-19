<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Structures\ArrayValue;
use \Smuuf\Primi\Structures\NumberValue;

class ArrayDefinition extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		if (empty($node['items'])) {
			return new ArrayValue([]);
		}

		Common::ensureIndexed($node['items']);
		return new ArrayValue(self::buildArray($node['items'], $context));

	}

	protected static function buildArray(array $itemNodes, Context $context): array {

		$result = [];
		$index = 0;

		foreach ($itemNodes as $itemNode) {

			// Key doesn't have to be defined.
			if (isset($itemNode['key'])) {

				// But if it is defined for this item, use it.
				$keyHandler = HandlerFactory::get($itemNode['key']['name']);
				$key = $keyHandler::handle($itemNode['key'], $context);
				$key = $key->getInternalValue();

				// And if it is a numeric integer, use it as a base for the
				// index counter we would have used if the key was not provided.
				if (NumberValue::isNumericInt($key)) {
					$index = $key + 1;
				}

			} else {

				// The key was not provided, so assign a key for this item using
				// our private index counter.
				$key = $index++;

			}

			$valueHandler = HandlerFactory::get($itemNode['value']['name']);
			$value = $valueHandler::handle($itemNode['value'], $context);

			$result[$key] = $value;

		}

		return $result;

	}

}
