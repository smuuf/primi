<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\InternalUndefinedIndexException;
use \Smuuf\Primi\UndefinedIndexException;
use \Smuuf\Primi\ISupportsDereference;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers;

class Dereference extends \Smuuf\Primi\StrictObject implements IChainedHandler {

	public static function chain(array $node, Context $context, \Smuuf\Primi\Structures\Value $subject) {

		if (!$subject instanceof ISupportsDereference) {
			throw new \Smuuf\Primi\ErrorException(sprintf(
				"Type '%s' does not support dereferencing",
				$subject::TYPE
			), $node);
		}

		try {

			$handler = HandlerFactory::get($node['key']['name']);
			$key = $handler::handle($node['key'], $context);
			return $subject->dereference($key);

		} catch (InternalUndefinedIndexException $e) {
			throw new UndefinedIndexException($e->getMessage(), $node);
		}

	}

}
