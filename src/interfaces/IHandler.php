<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;

/**
 * An AST node handler must implement this interface.
 */
interface IHandler {

	public static function handle(array $node, Context $context);

}
