<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\InternalUndefinedPropertyException;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Context;

class PropertyGetter extends \Smuuf\Primi\StrictObject implements IChainedHandler {

	public static function chain(array $node, Context $context, Value $subject) {

		$handler = HandlerFactory::get($node['key']['name']);
		$propName = $handler::handle($node['key'], $context);

		try {
			return $subject->propertyGet($propName);
		} catch (InternalUndefinedPropertyException $e) {

			throw new ErrorException(sprintf(
				"Undefined property '%s' of type '%s'.",
				$propName,
				$subject::TYPE
			), $node);

		}

	}

}
