<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\UndefinedIndexException;
use \Smuuf\Primi\InternalUndefinedIndexException;

use \Smuuf\Primi\ISupportsDereference;
use \Smuuf\Primi\ISupportsInsertion;

use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class VariableVector extends \Smuuf\Primi\Object implements IHandler {

	/**
	 * This handler returns a final part of the chain - a value object that's derived from the vector and which
	 * supports insertion. All values but the last part of the chain also must support dereferencing.
	 */
	public static function handle(array $node, Context $context) {

		if (!isset($node['vector'][0])) {
			$node['vector'] = [$node['vector']];
		}

		// Obtain the origin value.
		// This is the value the vector of keys/indexes starts with.
		$target = HandlerFactory
			::get($node['core']['name'])
			::handle($node['core'], $context);

		// Extract the last part of the vector, because it needs special treatment.
		$lastPart = \array_pop($node['vector']);

		try {

			foreach ($node['vector'] as $part) {

				if (!$target instanceof ISupportsDereference) {
					throw new ErrorException(sprintf(
						"Value type '%s' does not support dereferencing.",
						$target::TYPE
					), $node);
				}

				$handler = HandlerFactory::get($part['name']);
				$key = $handler::handle($part, $context);

				$target = $target->dereference($key);

			}

		} catch (InternalUndefinedIndexException $e) {
			throw new UndefinedIndexException($e->getMessage(), $node);
		}

		// The last part of the chain must support insertion, because that's the value object we're actually later
		// going to put the new value into.
		if (!$target instanceof ISupportsInsertion) {
			throw new ErrorException(sprintf(
				"Value type '%s' does not support insertion.",
				$target::TYPE
			), $node);
		}

		if ($lastPart['name'] === "vector") {

			// "vector" signals the key name was empty - the value should be insetred at the end.
			$key = "";

		} else {

			// Determine the final new key for the value that will be insetred.
			$handler = HandlerFactory::get($lastPart['name']);
			$key = (string) $handler::handle($lastPart, $context)->getPhpValue();

		}

		// Build insertion proxy with pre-configured key, so the Assignment handler itself doesn't have to be aware
		// of the specific key later.
		// (This saves us the trouble of having to somehow return back both the target object AND the key.)
		return $target->getInsertionProxy($key);

	}

}
