<?php

namespace Smuuf\Primi;

class HandlerFactory extends \Smuuf\Primi\StrictObject {

	protected static $cache = [];

	public static function get(string $name) {

		// Using caching should be faster than repeatedly building strings and checking classes and stuff.
		if (isset(self::$cache[$name])) {
			return self::$cache[$name];
		}

		$className = __NAMESPACE__ . "\\Handlers\\$name";
		$result = class_exists($className) ? $className : false;

		self::$cache[$name] = $result;
		return $result;

	}

}
