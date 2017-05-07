<?php

namespace Smuuf\Primi;

class HandlerFactory extends \Smuuf\Primi\Object {

	public static function get($name) {

		$class = __NAMESPACE__ . "\\Handlers\\$name";

		if (!is_subclass_of($class, __NAMESPACE__ . '\Handlers\IHandler')) {
			throw new \LogicException("'$name' handler not found.");
		}

		return $class;

	}

}