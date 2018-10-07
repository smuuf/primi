<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class Block extends \Smuuf\Primi\StrictObject implements IHandler, IReducer {

	public static function handle(array $node, Context $context) {

		// This handler shouldn't even be needed, if it had an inside node.
		// This method is here just to implement the IHandler interface.

	}

	public static function reduce(array $node) {

		// ParserHandler reduces the "skip" node automatically.
		// But if it is not present, a more complex node would be returned,
		// because it's not going to be automatically reduced.

		// We don't want that. Return an empty array instead.
		return [];

	}

}
