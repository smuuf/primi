<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\FnContainer;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\FuncValue;

class ExtensionHub extends \Smuuf\Primi\StrictObject {

	protected static $extensions = [];

	public static function add($extension, string $target = null) {

		// Handle possible group registering.
		if (is_array($extension)) {
			foreach ($extension as $ext => $target) {
				self::add($ext, $target);
			}
			return;
		}

		if (!is_subclass_of($extension, \Smuuf\Primi\Extension::class)) {
			throw new \LogicException("'$extension' is not a valid extension.");
		}

		if (!isset(self::$extensions[$target])) {
			self::$extensions[$target] = [];
		}

		$processed = self::process($extension);
		self::$extensions[$target] = array_replace(self::$extensions[$target], $processed);

	}

	public static function get($target): array {
		return self::$extensions[$target] ?? [];
	}

	protected static function process(string $class): array {

		$classRef = new \ReflectionClass($class);
		$methods = $classRef->getMethods(\ReflectionMethod::IS_PUBLIC);
		$instance = new $class;
		$result = [];

		foreach ($methods as $methodRef) {

			$methodName = $methodRef->getName();

			// Skip magic methods.
			if (strpos($methodName, '__') === 0) {
				continue;
			}

			// Extensions must provide return types for its functions.
			$value = $instance->$methodName();
			if (!$value instanceof Value) {
				$value = Value::buildAutomatic($value);
			}

			$result[$methodName] = $value;

		}

		return $result;

	}

}
