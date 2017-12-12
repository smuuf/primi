<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;

/**
 * Interface representing handler that supports doing stuff on top of value object passed in.
 */
interface IChainedHandler {

	public static function chain(array $node, Context $context, \Smuuf\Primi\Structures\Value $subject);

}
