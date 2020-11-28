<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\SystemException;
use \Smuuf\Primi\Ex\ContextAwareException;

/**
 * Base node handler class for evaluating some AST node within given context.
 */
abstract class SimpleHandler extends Handler {

	private const EVENT_TICK_RESOLUTION = 100;
	private static $tick = 0;

	final public static function run(
		array $node,
		Context $context
	) {

		try {

			if (++self::$tick >= self::EVENT_TICK_RESOLUTION) {

				if ($event = $context->getEvent()) {
					if ($event === 'SIGINT') {
						throw new SystemException('Received SIGINT');
					}
				}

				self::$tick = 0;

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
