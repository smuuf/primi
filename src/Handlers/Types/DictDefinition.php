<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\UnhashableTypeException;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Values\DictValue;

class DictDefinition extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		if (empty($node['items'])) {
			return new DictValue;
		}

		try {

			return new DictValue(Func::iterator_as_tuples(
				self::buildMap($node['items'], $context)
			));

		} catch (UnhashableTypeException $e) {
			throw new RuntimeError(\sprintf(
				"Cannot create dict with key of unhashable type '%s'",
				$e->getType()
			));
		}

	}

	protected static function buildMap(
		array $itemNodes,
		Context $context
	): \Generator {

		$result = [];
		foreach ($itemNodes as $itemNode) {

			$keyHandler = HandlerFactory::getFor($itemNode['key']['name']);
			$valueHandler = HandlerFactory::getFor($itemNode['value']['name']);
			yield $keyHandler::run($itemNode['key'], $context)
				=> $valueHandler::run($itemNode['value'], $context);;

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