<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;

/**
 * This interface tells that a AST node handler knows how to reduce (simplify) itself.
 */
interface IReducer {

	public static function reduce(array $node);

}
