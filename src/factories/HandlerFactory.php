<?php

declare(strict_types=1);

namespace Smuuf\Primi;

class HandlerFactory extends \Smuuf\Primi\StrictObject {

	protected static $cache = [];

	public static function get(string $name, ?bool $strict = true) {

		// Using caching is faster than repeatedly building strings and checking
		// classes and stuff.
		$class = self::$cache[$name] ?? (
			\class_exists($class = __NAMESPACE__ . "\\Handlers\\$name")
				? $class
				: \false
		);

		if ($class === \false && $strict) {
			throw new \LogicException("Handler '$name' not found.");
		}

		self::$cache[$name] = $class;
		return $class;

	}

}
