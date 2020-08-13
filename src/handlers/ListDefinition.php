<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Structures\ListValue;

use function \Smuuf\Primi\Helpers\ensure_indexed as primifn_ensure_indexed;

class ListDefinition extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		if (empty($node['items'])) {
			return new ListValue([]);
		}

		return new ListValue(self::buildValues($node['items'], $context));

	}

	protected static function buildValues(
		array $itemNodes,
		Context $context
	): array {

		$result = [];
		foreach ($itemNodes as $itemNode) {

			$valueHandler = HandlerFactory::get($itemNode['name']);
			$value = $valueHandler::handle($itemNode, $context);

			$result[] = $value;

		}

		return $result;

	}

	public static function reduce(array &$node): void {

		// Make sure this is always list, even with one item.
		if (isset($node['items'])) {
			$node['items'] = primifn_ensure_indexed($node['items']);
		}

	}

}
