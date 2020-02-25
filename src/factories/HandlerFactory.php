<?php

namespace Smuuf\Primi;

class HandlerFactory extends \Smuuf\Primi\StrictObject {

	protected static $cache = [];

	public static function get(string $name, ?bool $strict = true) {

		// Using caching should be faster than repeatedly building strings and checking classes and stuff.
		if (isset(self::$cache[$name])) {
			return self::$cache[$name];
		}

		$className = __NAMESPACE__ . "\\Handlers\\$name";
		if (!class_exists($className)) {
			if ($strict) {
				throw new \LogicException("Handler '$className' not found.");
			}
			$className = false;
		}

		self::$cache[$name] = $className;
		return $className;

	}

}
