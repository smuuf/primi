<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Location;
use \Smuuf\Primi\Ex\SyntaxError;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\ErrorException;
use \Smuuf\Primi\Ex\SystemException;

/**
 * Base node handler class for evaluating some AST node within given context.
 */
abstract class SimpleHandler extends Handler {

	final public static function run(
		array $node,
		Context $context
	) {

		try {
			return static::handle($node, $context);
		} catch (RuntimeError|SyntaxError|SystemException $e) {

			$location = new Location(
				$context->getCurrentModule()->getStringRepr(),
				(int) $node['_l'],
				(int) $node['_p']
			);

			throw new ErrorException(
				$e->getMessage(),
				$location,
				$context->getCallStack()
			);

		}

	}

	abstract protected static function handle(array $node, Context $context);

}
