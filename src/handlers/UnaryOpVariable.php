<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\InternalException;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class UnaryOpVariable extends \Smuuf\Primi\StrictObject implements IHandler {

	public static function handle(array $node, Context $context) {

		if (isset($node['pre']) || isset($node['post'])) {
			return UnaryOperator::handle($node, $context);
		} else {
			throw new InternalException(
				"Encountered UnaryOpVariable node without any unary operator"
			);
		}

	}

}
