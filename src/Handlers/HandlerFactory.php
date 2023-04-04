<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers;

use Smuuf\StrictObject;
use Smuuf\Primi\Ex\EngineInternalError;

/**
 * Static helper class for getting correct handler class for specific AST node.
 *
 * @internal
 */
abstract class HandlerFactory {

	use StrictObject;

	/** @var array<string, string|null> Dict of handler classes we know exist. */
	private static $handlersCache = [];

	private const PREFIX = 'Smuuf\Primi\Handlers\Kinds';

	/**
	 * @return class-string|string
	 */
	private static function buildHandlerClassName(string $name) {
		return self::PREFIX . "\\$name";
	}

	/**
	 * NOTE: This is used only during parsing/compilation.
	 *
	 * @return class-string|string
	 */
	public static function tryGetFor(string $name): ?string {

		$class = self::buildHandlerClassName($name);
		if (!\class_exists($class)) {
			return null;
		}

		return $class;

	}

	/**
	 * Get handler class as string for a AST-node-type specific handler
	 * identified by handler type.
	 *
	 * NOTE: Return type is omitted for performance reasons, as this method will
	 * be called VERY often.
	 *
	 * @param string $name
	 * @return ?class-string
	 */
	public static function getFor(string $name): string {

		// Using caching is of course faster than repeatedly building strings
		// and checking classes and stuff.
		if (\array_key_exists($name, self::$handlersCache)) {
			return self::$handlersCache[$name];
		}

		$class = self::tryGetFor($name);
		if ($class === \null) {
			$msg = "Handler class for '$name' not found";
			throw new EngineInternalError($msg);
		}

		return self::$handlersCache[$name] = $class;

	}

}
