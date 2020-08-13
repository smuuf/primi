<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\ISupportsKeyAccess;
use \Smuuf\Primi\Ex\LookupError;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Helpers\ChainedHandler;
use \Smuuf\Primi\Structures\Value;

class Dereference extends ChainedHandler {

	public static function chain(
		array $node,
		Context $context,
		Value $subject
	) {

		if (!$subject instanceof ISupportsKeyAccess) {
			throw new RuntimeError(\sprintf(
				"Type '%s' does not support dereferencing",
				$subject::TYPE
			), $node);
		}

		try {

			$handler = HandlerFactory::get($node['key']['name']);
			$key = $handler::handle($node['key'], $context);

			if (!\is_scalar($key->getInternalValue())) {
				$type = $key::TYPE;
				throw new LookupError("Cannot use '$type' for lookup.");
			}

			return $subject->arrayGet($key->getInternalValue());

		} catch (LookupError $e) {
			throw new RuntimeError($e->getMessage(), $node);
		}

	}

}
