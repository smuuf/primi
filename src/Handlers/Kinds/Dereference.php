<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Handlers\ChainedHandler;
use \Smuuf\Primi\Handlers\HandlerFactory;

class Dereference extends ChainedHandler {

	public static function chain(
		array $node,
		Context $context,
		AbstractValue $subject
	) {

		$key = HandlerFactory::runNode($node['key'], $context);

		$returned = $subject->itemGet($key);
		if ($returned === \null) {
			throw new RuntimeError(\sprintf(
				"Type '%s' does not support item access",
				$subject->getTypeName()
			));
		}

		return $returned;

	}

}
