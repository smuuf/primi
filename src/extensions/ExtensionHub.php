<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\FunctionContainer;
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

	public static function get($target) {
		return self::$extensions[$target];
	}

	protected static function process(string $class): array {

		$classRef = new \ReflectionClass($class);
		$methods = $classRef->getMethods(~\ReflectionMethod::IS_STATIC | \ReflectionMethod::IS_PUBLIC);
		$instance = new $class;
		$result = [];

		foreach ($methods as $methodRef) {

			$methodName = $methodRef->getName();

			// Skip magic methods.
			if (strpos($methodName, '__') === 0) {
				continue;
			}

			// Extensions must provide return types for its functions.
			$returnType = $methodRef->getReturnType();
			if (!$returnType) {
				throw new \LogicException("Extension method '$class::{$methodRef->name}()' must have return type specified.");
			}

			// And that return type must be represent a value.
			$returnType = $returnType->getName();
			if (!is_a($returnType, \Smuuf\Primi\Structures\Value::class, true)) {
				throw new \LogicException("Extension method '$class::{$methodRef->name}()' has wrong return type '$returnType'.");
			}

			$callable = [$instance, $methodName];
			$container = FunctionContainer::buildExtensionFunction($callable);
			$result[$methodName] = new FuncValue($container);

		}

		return $result;

	}

}
