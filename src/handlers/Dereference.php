<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\ISupportsArrayAccess;
use \Smuuf\Primi\Helpers\ChainedHandler;
use \Smuuf\Primi\UndefinedIndexException;
use \Smuuf\Primi\InternalUndefinedIndexException;

class Dereference extends ChainedHandler {

	public static function chain(
		array $node,
		Context $context,
		Value $subject
	) {

		if (!$subject instanceof ISupportsArrayAccess) {
			throw new \Smuuf\Primi\ErrorException(\sprintf(
				"Type '%s' does not support dereferencing",
				$subject::TYPE
			), $node);
		}

		try {

			$handler = HandlerFactory::get($node['key']['name']);
			$key = $handler::handle($node['key'], $context);
			return $subject->arrayGet($key->getInternalValue());

		} catch (InternalUndefinedIndexException $e) {
			throw new UndefinedIndexException($e->getMessage(), $node);
		}

	}

}
