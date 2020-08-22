<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Structures\DictValue;

class DictDefinition extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		if (empty($node['items'])) {
			return new DictValue([]);
		}

		return new DictValue(self::buildItems($node['items'], $context));

	}

	protected static function buildItems(
		array $itemNodes,
		Context $context
	): array {

		$result = [];
		foreach ($itemNodes as $itemNode) {

			$keyHandler = HandlerFactory::get($itemNode['key']['name']);
			$key = $keyHandler::handle($itemNode['key'], $context);
			$key = $key->getInternalValue();

			$valueHandler = HandlerFactory::get($itemNode['value']['name']);
			$value = $valueHandler::handle($itemNode['value'], $context);

			$result[$key] = $value;

		}

		return $result;

	}

	public static function reduce(array &$node): void {

		// Make sure this is always list, even with one item.
		if (isset($node['items'])) {
			$node['items'] = Func::ensure_indexed($node['items']);
		}

	}

}
