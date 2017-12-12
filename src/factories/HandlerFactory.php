<?php

namespace Smuuf\Primi;

class HandlerFactory extends \Smuuf\Primi\StrictObject {

	protected static $cache = [];

	public static function get($name) {

		// Using caching should be faster than repeatedly building strings and checking classes and stuff.
		if (isset(self::$cache[$name])) {
			return self::$cache[$name];
		}

		return self::$cache[$name] = __NAMESPACE__ . "\\Handlers\\$name";

	}

}
