<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\UnhashableTypeException;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Structures\DictValue;

class DictDefinition extends SimpleHandler {

	public static function handle(array $node, Context $context) {

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
			), $node);
		}

	}

	protected static function buildMap(
		array $itemNodes,
		Context $context
	): \Generator {

		$result = [];
		foreach ($itemNodes as $itemNode) {

			$keyHandler = HandlerFactory::get($itemNode['key']['name']);
			$valueHandler = HandlerFactory::get($itemNode['value']['name']);
			yield $keyHandler::handle($itemNode['key'], $context)
				=> $valueHandler::handle($itemNode['value'], $context);;

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
