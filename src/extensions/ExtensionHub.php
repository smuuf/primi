<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\FnContainer;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\FuncValue;

class ExtensionHub extends \Smuuf\Primi\StrictObject {

	protected static $extensions = [];

	/**
	 * Register a PHP class as an extension to a target Primi  <...>Value class.
	 * Optionally pass an array of <PHP class> => <Value class> pairs to
	 * register multiple extensions at once.
	 */
	public static function add($extension, string $target = null) {

		// Handle possible multiple registering.
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

	/**
	 * This returns unique clones of each extension method's return values.
	 * Clones because whatever someone then does to the objects we return from
	 * here, the changes will be localised.
	 *
	 * We need to always return a fresh copy!
	 */
	public static function get($target): array {
		return self::$extensions[$target]
			? array_map(function($item) {
				return clone $item;
			}, self::$extensions[$target])
			: [];
	}

	/**
	 * Process an extension class - iterate over all public methods and
	 * register their return value as target value object's property.
	 * We'll use the method's name as the target property name.
	 *
	 * For example, if you register "SomeExtensionClass::class" as an extension
	 * to the Primi's "StringValue::class", then if, for example, you write a
	 * "SomeExtensionClass::plsGimmeOne()" method which returns a new
	 * NumberValue(1), that Number value will be available under the
	 * <code>'hello there!'.plsGimmeOne; </code> property.
	 *
	 * Then, if you use <code>'hello there!'.plsGimmeOne</code>, in
	 * some Primi source code, it will return a number one.
	 */
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
