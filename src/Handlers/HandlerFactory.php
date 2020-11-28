<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\StrictObject;
use \Smuuf\Primi\Ex\EngineInternalError;

/**
 * Static helper class for getting correct handler class for specific AST node.
 */
abstract class HandlerFactory extends StrictObject {

	/** @var array<string, string|null> Dict of handler classes we know exist. */
	private static $handlersCache = [];

	private const PREFIX = 'Smuuf\\Primi\\Handlers\\Types\\';

	/**
	 * Get class for a AST-node-type specific handler identified by handler
	 * type.
	 *
	 * Return type is omitted for performance reasons, as this method will be
	 * called VERY often.
	 *
	 * @return string
	 */
	final public static function getFor(string $name, ?bool $strict = true) {

		// Using caching is faster than repeatedly building strings and checking
		// classes and stuff.
		$class = self::$handlersCache[$name] ?? (
			\class_exists($class = self::PREFIX . $name)
				? $class
				: \null
		);

		if ($class === \null && $strict) {
			throw new EngineInternalError("Handler type '$name' not found");
		}

		return self::$handlersCache[$name] = $class;

	}

}
