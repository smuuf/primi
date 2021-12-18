<?php

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\UnhashableTypeException;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Values\DictValue;

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
			throw new RuntimeError(\sprintf(
				"Cannot create dict with key containing unhashable type '%s'",
				$e->getType()
			));
		}

	}

	private static function buildPairs(
		array $nodes,
		Context $context
	): array {

		$result = [];
		foreach ($nodes as $node) {

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
