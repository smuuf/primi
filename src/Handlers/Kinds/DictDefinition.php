<?php

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Ex\UnhashableTypeException;
use \Smuuf\Primi\Values\DictValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Handlers\SimpleHandler;

class DictDefinition extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		if (!$node['items']) {
			return new DictValue;
		}

		try {

			return new DictValue(
				self::buildPairs($node['items'], $context)
			);

		} catch (UnhashableTypeException $e) {
			throw new TypeError(\sprintf(
				"Cannot create dict with key containing unhashable type '%s'",
				$e->getType()
			));
		}

	}

	/**
	 * @param array<TypeDef_AstNode> $itemNodes
	 * @return TypeDef_PrimiObjectCouples
	 */
	private static function buildPairs(
		array $itemNodes,
		Context $context
	): iterable {

		$result = [];
		foreach ($itemNodes as $node) {

			$result[] = [
				HandlerFactory::runNode($node['key'], $context),
				HandlerFactory::runNode($node['value'], $context),
			];

		}

		return $result;

	}

	public static function reduce(array &$node): void {

		// Make sure this is always list, even with one item.
		if (isset($node['items'])) {
			$node['items'] = Func::ensure_indexed($node['items']);
		} else {
			$node['items'] = [];
		}

	}

}
