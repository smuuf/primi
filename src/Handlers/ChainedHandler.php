<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Values\AbstractValue;

/**
 * Base node handler class for evaluating some AST node within given context
 * with passed (chained) AbstractValue object.
 */
abstract class ChainedHandler extends Handler {

	abstract public static function chain(
		array $node,
		Context $context,
		AbstractValue $subject
	);

}
