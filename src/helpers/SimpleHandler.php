<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\SystemException;
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

			if ($event = $context->getEvent()) {
				if ($event === 'SIGINT') {
					throw new SystemException('Received SIGINT');
				}
			}

			return static::handle($node, $context);

		} catch (RuntimeError|SystemException $e) {
			throw new ContextAwareException($e->getMessage(), $node, $context);
		}

	}

	abstract protected static function handle(
		array $node,
		Context $context
	);

}
