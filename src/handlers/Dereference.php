<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Helpers\ChainedHandler;
use \Smuuf\Primi\Structures\Value;

class Dereference extends ChainedHandler {

	public static function chain(
		array $node,
		Context $context,
		Value $subject
	) {

		$handler = HandlerFactory::get($node['key']['name']);
		$key = $handler::run($node['key'], $context);

		$returned = $subject->itemGet($key);
		if ($returned === null) {
			throw new RuntimeError(\sprintf(
				"Type '%s' does not support item access",
				$subject::TYPE
			));
		}

		return $returned;

	}

}
