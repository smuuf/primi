<?php

namespace Smuuf\Primi;

class HandlerFactory extends \Smuuf\Primi\Object {

	protected static $cache = [];

	public static function get($name, $strict = true) {

		if (isset(self::$cache[$name])) {
			return self::$cache[$name];
		}

		$class = __NAMESPACE__ . "\\Handlers\\$name";
		if (!is_subclass_of($class, __NAMESPACE__ . '\Handlers\IHandler')) {

			if (!$strict) {
				return false;
			}

			throw new \LogicException("'$name' handler not found.");

		}

		return self::$cache[$name] = $class;

	}

}
