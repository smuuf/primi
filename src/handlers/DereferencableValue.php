<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\InternalUndefinedIndexException;
use \Smuuf\Primi\UndefinedIndexException;
use \Smuuf\Primi\ISupportsDereference;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers;

class DereferencableValue extends \Smuuf\Primi\StrictObject implements IHandler, IReducer {

	public static function handle(array $node, Context $context) {

		$handler = HandlerFactory::get($node['core']['name']);
		$value = $handler::handle($node['core'], $context);

		if (isset($node['dereference'])) {

			Helpers::ensureIndexed($node['dereference']);

			try {

				foreach ($node['dereference'] as $keyNode) {

					if (!$value instanceof ISupportsDereference) {
						throw new \Smuuf\Primi\ErrorException(sprintf(
							"Value type '%s' does not support dereferencing.",
							$value::TYPE
						), $node);
					}

					$handler = HandlerFactory::get($keyNode['name']);
					$key = $handler::handle($keyNode, $context);

					$value = $value->dereference($key);

				}

			} catch (InternalUndefinedIndexException $e) {
				throw new UndefinedIndexException($e->getMessage(), $node);
			}

		}

		return $value;

	}

	public static function reduce(array $node) {

		// If this node has any unary operator or dereference, don't reduce it.
		if (isset($node['dereference'])) {
			return;
		}

		// Otherwise reduce it to just the core node.
		return $node['core'];

	}

}
