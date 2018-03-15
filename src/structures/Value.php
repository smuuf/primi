<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\ValueFriends;
use \Smuuf\Primi\InternalUndefinedMethodException;

abstract class Value extends ValueFriends {

	const TYPE = "__no_type__";

	/** @var array Array of prioritized libraries used to call methods on values. **/
	protected static $libraries = [];

	public static function registerLibrary(string $libraryClass) {

		if (!is_subclass_of($libraryClass, \Smuuf\Primi\Library::class)) {
			throw new \LogicException("Cannot register '$libraryClass' which does not extend '\Smuuf\Primi\Library'.");
		}

		array_unshift(static::$libraries, $libraryClass);

	}

	public static function buildAutomatic($value) {

		switch (true) {
			case \is_bool($value):
				return new BoolValue($value);
			case \is_array($value):
				return new ArrayValue(array_map([self::class, 'buildAutomatic'], $value));
			case NumberValue::isNumeric($value): // Must be after "is_array" case.
				return new NumberValue($value);
			default:
				return new StringValue($value);
		}

	}

	public function getInternalValue() {
		return $this->value;
	}

	/**
	 * Call a method on this value.
	 * Engine uses this method as proxy to all value methods.
	 */
	public function call(string $method, array $args = []): Value {

		foreach (static::$libraries as $lib) {
			if (\method_exists($lib, $method)) {
				return $lib::{$method}($this, ...$args);
			}
		}

		throw new InternalUndefinedMethodException;

	}

	abstract public function getStringValue(): string;

	/**
	 * Throw new TypeException when the value does not match any of the types provided.
	 * Use this when allowing only certain types of values in call*() methods.
	 *
	 * This is needed because of PHP's imperfect type-hinting system which does not support union types.
	 *
	 * @throws \TypeException
	 */
	public static function allowTypes(?Value $value, string ...$types) {

		foreach ($types as $type) {
			if ($value instanceof $type) {
				return; // If any of the instanceof checks is true, the type is allowed.
			}
		}

		// The value did not match any of the types provided.
		throw new \TypeError;

	}

}
