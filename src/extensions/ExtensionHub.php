<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;

class ExtensionHub extends \Smuuf\Primi\StrictObject {

	protected static $extensions = [];

	/**
	 * Register a PHP class as an extension to a target Primi  <...>Value class.
	 * Optionally pass an array of <PHP class> => <Value class> pairs to
	 * register multiple extensions at once.
	 */
	public static function add($extension) {

		// We allow registering extensions in bulk.
		if (is_array($extension)) {
			foreach ($extension as $ext) {
				self::add($ext);
			}
			return;
		}

		if (!is_subclass_of($extension, \Smuuf\Primi\Extension::class)) {
			throw new \LogicException("'$extension' is not a valid extension.");
		}

		$processed = self::process($extension);
		self::$extensions = array_replace(self::$extensions, $processed);

	}

	/**
	 * Return array of values provided by all registered extensions.
	 */
	public static function get(): array {
		return self::$extensions;
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

		// We want methods that are both public AND static. And we have to do
		// this by intersection.
		// See http://php.net/manual/en/reflectionclass.getmethods.php.
		// "... all methods with any of the given attributes will be returned."
		$public = $classRef->getMethods(\ReflectionMethod::IS_PUBLIC);
		$static = $classRef->getMethods(\ReflectionMethod::IS_STATIC);
		$methods = array_intersect($static, $public);

		$result = [];
		foreach ($methods as $methodRef) {

			$methodName = $methodRef->getName();

			// Skip magic methods.
			if (strpos($methodName, '__') === 0) {
				continue;
			}

			$callable = [$class, $methodName];
			$value = Value::buildAutomatic($callable);
			$result[$methodName] = $value;

		}

		return $result;

	}

}
