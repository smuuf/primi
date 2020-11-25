<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\ContextAwareException;

/**
 * Base node handler class for evaluating some AST node within given context.
 */
abstract class SimpleHandler extends BaseHandler {

	final public static function run(
		array $node,
		Context $context
	) {

		try {
			return static::handle($node, $context);
		} catch (RuntimeError $e) {
			throw new ContextAwareException($e->getMessage(), $node, $context);
		}

	}

	abstract protected static function handle(
		array $node,
		Context $context
	);

}
