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

	private const PREFIX = '\Smuuf\Primi\Handlers\Kinds';

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
	public static function tryGetForName(string $name): ?string {

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
	public static function getFor($id) {

		// Using caching is of course faster than repeatedly building strings
		// and checking classes and stuff.
		if (\array_key_exists($id, self::$handlersCache)) {
			return self::$handlersCache[$id];
		}

		$class = self::buildHandlerClassName(KnownHandlers::fromId($id));
		if (!\class_exists($class)) {
			$msg = "Handler class '$class' for handler ID '$id' not found";
			throw new EngineInternalError($msg);
		}

		return self::$handlersCache[$id] = $class;

	}

	/**
	 * Shorthand function for running a AST node passed as array.
	 *
	 * @param TypeDef_AstNode $node
	 * @param Context $ctx
	 * @return mixed
	 */
	public static function runNode($node, $ctx) {
		return self::getFor($node['name'])::run($node, $ctx);
	}

}
