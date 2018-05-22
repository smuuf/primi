<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\ExtensionHub;
use \Smuuf\Primi\ISupportsPropertyAccess;
use \Smuuf\Primi\InternalUndefinedVariableException;
use \Smuuf\Primi\InternalUndefinedPropertyException;
use \Smuuf\Primi\Structures\ValueFriends;

abstract class Value extends ValueFriends implements ISupportsPropertyAccess {

	const TYPE = "__no_type__";

	/**
	 * This value object's own context (eg. contains properties, etc.)
	 * @var Context
	 */
	protected $properties;

	/** @var bool Are this value's properties initialized? **/
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
					return new FuncValue(FnContainer::buildFromClosure($value));
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

	public function propertyGet(string $name): Value {

		// Lazy load properties from extensions.
		if (!$this->propertiesInitialized) {
			$this->initProperties();
		}

		try {
			return $this->properties->getVariable($name);
		} catch (InternalUndefinedVariableException $e) {
			throw new InternalUndefinedPropertyException;
		}

	}

	public function propertySet(string $key, Value $value) {

		// Lazy load properties from extensions.
		if (!$this->propertiesInitialized) {
			$this->initProperties();
		}

		$this->properties->setVariable($key, $value);
		return $value;

	}

	public function getPropertyInsertionProxy(string $key): PropertyInsertionProxy {
		return new PropertyInsertionProxy($this, $key);
	}

	private function initProperties() {

		$this->properties = new Context($this);

		$items = \Smuuf\Primi\ExtensionHub::get(static::class);
		$this->properties->setVariables($items);
		$this->propertiesInitialized = true;

	}

	/**
	 * Shorthand for programatically getting the bound function (method) and
	 * invoking it with optionally specified argumments.
	 */
	public function call(string $name, $args = []): Value {
		return $this->propertyGet($name)->invoke($args);
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
