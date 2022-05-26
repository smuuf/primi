<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Values\ListValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Handlers\HandlerFactory;

class ListDefinition extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		if (empty($node['items'])) {
			return new ListValue([]);
		}

		return new ListValue(self::buildValues($node['items'], $context));

	}

	/**
	 * @param array<TypeDef_AstNode> $itemNodes
	 * @return array<AbstractValue>
	 */
	protected static function buildValues(
		array $itemNodes,
		Context $context
	): array {

		$result = [];
		foreach ($itemNodes as $itemNode) {
			$result[] = HandlerFactory::runNode($itemNode, $context);
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
