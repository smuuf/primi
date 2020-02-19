<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;

/**
 * An AST node handler must implement this interface.
 */
interface IHandler {

	const NODE_NEEDS_TEXT = false;

	public static function handle(array $node, Context $context);

}
