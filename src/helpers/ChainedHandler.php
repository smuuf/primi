<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Structures\Value;

/**
 * Base node handler class for evaluating some AST node within given context
 * with passed (chained) Value object.
 */
abstract class ChainedHandler extends BaseHandler {

	abstract public static function chain(
		array $node,
		Context $context,
		Value $subject
	);

}
