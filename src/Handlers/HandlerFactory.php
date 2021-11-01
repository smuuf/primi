<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\EngineInternalError;

/**
 * Static helper class for getting correct handler class for specific AST node.
 *
 * @internal
 */
abstract class HandlerFactory {

	use StrictObject;

	/** @var array<string, string|null> Dict of handler classes we know exist. */
	private static $handlersCache = [];

	private const PREFIX = \Smuuf\Primi\Handlers\Kinds::class;

	private static function getClassName(string $nodeName) {
		return self::PREFIX . "\\$nodeName";
	}

	/**
	 * Get handler class as string for a AST-node-type specific handler
	 * identified by handler type.
	 *
	 * NOTE: Return type is omitted for performance reasons, as this method will
	 * be called VERY often.
	 *
	 * @return string
	 */
	public static function getFor(string $name, ?bool $strict = \true) {

		// Using caching is of course faster than repeatedly building strings
		// and checking classes and stuff.
		if (isset(self::$handlersCache[$name])) {
			return self::$handlersCache[$name];
		}

		if (!\class_exists($class = self::getClassName($name))) {

			if ($strict) {
				throw new EngineInternalError("Handler type '$name' not found");
			}

			return \null;

		}

		return self::$handlersCache[$name] = $class;

	}

	/**
	 * Shorthand function for running a AST node passed as array.
	 */
	public static function runNode(array $node, Context $ctx) {
		return self::getFor($node['name'])::run($node, $ctx);
	}

}
