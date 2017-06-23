<?php

namespace Smuuf\Primi;

class HandlerFactory extends \Smuuf\Primi\Object {

	protected static $cache = [];

	public static function get($name) {

		$class = __NAMESPACE__ . "\\Handlers\\$name";

		if (isset(self::$cache[$class])) {
			return self::$cache[$class];
		}

		if (!is_subclass_of($class, __NAMESPACE__ . '\Handlers\IHandler')) {
			throw new \LogicException("'$name' handler not found.");
		}

		return self::$cache[$class] = $class;

	}

}