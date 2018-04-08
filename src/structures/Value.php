<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Structures\ValueFriends;
use \Smuuf\Primi\InternalUndefinedMethodException;

abstract class Value extends ValueFriends {

	const TYPE = "__no_type__";

	public static function buildAutomatic($value) {

		switch (true) {
			case $value === null:
				return new NullValue;
			case \is_bool($value):
				return new BoolValue($value);
			case \is_array($value):
				return new ArrayValue(array_map([self::class, 'buildAutomatic'], $value));
			case \is_callable($value);
					return new FuncValue(FunctionContainer::buildNative($value));
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

		// Get extensions registered for this class.
		$exts = \Smuuf\Primi\ExtensionHub::get(static::class);

		foreach ($exts as $ext) {
			if (\method_exists($ext, $method)) {
				return $ext::{$method}($this, ...$args);
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
