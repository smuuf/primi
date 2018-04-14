<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Structures\ValueFriends;
use \Smuuf\Primi\ExtensionHub;
use \Smuuf\Primi\InternalUndefinedPropertyException;

abstract class Value extends ValueFriends {

	const TYPE = "__no_type__";

	/**
	 * @var array<Value>
	 * Properties of this value object.
	 */
	protected $properties = [];

	/** @var bool **/
	protected $propertiesInitialized = false;

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

	abstract public function getStringValue(): string;

	/**
	 * Call a method on this value.
	 * Engine uses this method as proxy to all value methods.
	 */
	public function getProperty(string $name): Value {

		if (!$this->propertiesInitialized) {
			$this->initProperties();
		}

		if (isset($this->properties[$name])) {
			return $this->properties[$name];
		}

		throw new InternalUndefinedPropertyException;

	}

	/**
	 * Load properties from globally registered extensions.
	 */
	private function initProperties() {

		$items = \Smuuf\Primi\ExtensionHub::get(static::class);
		foreach ($items as $name => &$item) {

			// If the value is a function, set "this" value instance to to
			// as the function's "self". See FuncValue::bind() for details.
			if ($item instanceof FuncValue) {
				$item->bind($this);
			}

		}

		// Don't overwrite any previously set properties.
		$this->properties = $this->properties + $items;

	}

	/**
	 * Shorthand for programatically getting the bound function (method) and
	 * invoking it with optionally specified argumments.
	 */
	public function call(string $name, $args = []): Value {
		return $this->getProperty($name)->invoke($args);
	}

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
