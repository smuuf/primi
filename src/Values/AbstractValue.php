<?php

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Ex\UnhashableTypeException;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\ValueFriends;
use \Smuuf\Primi\Structures\FnContainer;

abstract class AbstractValue extends ValueFriends {

	/** @const string Name of Primi (value) type. */
	const TYPE = "any";

	/**
	 * Take any PHP value and convert it into  a Primi value object of
	 * appropriate type.
	 */
	public static function buildAuto($value) {

		switch (\true) {
			case $value === \null:
				return NullValue::build();
			case \is_bool($value):
				return BoolValue::build($value);
			case \is_int($value) || \is_float($value) || \is_numeric($value):
				return NumberValue::build(Func::scientific_to_decimal((string) $value));
			case \is_callable($value);
				// Must be before "is_array" case, because some "arrays"
				// can be in reality "callables".
				return new FuncValue(FnContainer::buildFromClosure($value));
			case \is_array($value):
				$inner = \array_map(__METHOD__, $value);
				if (Func::is_array_dict($value)) {
					return new DictValue(Func::php_array_to_dict_pairs($inner));
				} else {
					return new ListValue($inner);
				}
			case $value instanceof AbstractValue:
				return $value;
			default:
				return StringValue::build($value);
		}

	}

	/**
	 * Return a new - or existing - instance of value object. Use this for
	 * immutable values when it may make sense not to create many of separate
	 * objects representing the same value (which saves memory).
	 *
	 * @param mixed $value Whatever is to be passed into the specific-type value
	 * object constructor.
	 */
	public static function build($value = null) {
		throw new EngineError(sprintf(
			"%s does not implement factory method. Create new object directly",
			get_called_class()
		));
	}

	/**
	 * Returns the core PHP value of this Primi value object.
	 */
	final public function getInternalValue() {
		return $this->value;
	}

	/**
	 * Returns a string representation of internal value.
	 */
	public function getStringValue(): string {
		return $this->getStringRepr();
	}

	// Length.

	/**
	 * Values can report the length of it (i.e. its internal value).
	 * Values without any meaningful length can report null (default).
	 */
	public function getLength(): ?int {
		return \null;
	}

	/**
	 * Returns an unambiguous string representation of internal value.
	 *
	 * If possible, is should be in such form that it the result of this
	 * method can be used as Primi source code to recreate that value.
	 */
	abstract public function getStringRepr(): string;

	//
	// Truthiness.
	//

	/**
	 * All values must be able to tell if they're truthy or falsey.
	 * All values are truthy unless they tell otherwise.
	 */
	public function isTruthy(): bool {
		return \true;
	}

	//
	// Comparisons - Equality.
	//

	/**
	 * All values support comparison.
	 *
	 * Default implementation below says that two values are equal if they're
	 * the same PHP object.
	 */
	public function isEqualTo(
		AbstractValue $right
	): ?bool {
		return $this === $right;
	}

	//
	// Comparisons - Relation.
	//

	/**
	 * If a value knows how to evaluate relation to other values, it shall
	 * define that by overriding this default logic. (By default a value does
	 * not know anything about its relation of itself to other values.)
	 *
	 * Relation in this scope means the result of <, >, <=, >= operations.
	 */
	public function hasRelationTo(string $operator, AbstractValue $right): ?bool {
		return \null;
	}

	public function doesContain(AbstractValue $right): ?bool {
		return \null;
	}

	public function doAddition(AbstractValue $right): ?AbstractValue {
		return \null;
	}

	public function doSubtraction(AbstractValue $right): ?AbstractValue {
		return \null;
	}

	public function doMultiplication(AbstractValue $right): ?AbstractValue {
		return \null;
	}

	public function doDivision(AbstractValue $right): ?AbstractValue {
		return \null;
	}

	public function doPower(AbstractValue $right): ?AbstractValue {
		return \null;
	}

	/**
	 * Called when used as `some_value()`.
	 *
	 * If value supports invocation, it must return a AbstractValue. Otherwise return
	 * null.
	 *
	 * @param Context $context Runtime context of the call-site.
	 * @param array<AbstractValue> $args Array dictionary of call arguments.
	 */
	public function invoke(
		Context $context,
		array $args = []
	): ?AbstractValue {
		return \null;
	}

	public function getIterator(): ?\Iterator {
		return \null;
	}

	/**
	 * Assign a value under specified key into this value.
	 *
	 * Must return true on successful assignment, or `false` if assignment is
	 * not supported.
	 */
	public function itemSet(?AbstractValue $key, AbstractValue $value): bool {
		return \false;
	}

	/**
	 * Returns some internal value by specified key.
	 *
	 * Must return some value object, or `null` if such operation is not
	 * supported.
	 */
	public function itemGet(AbstractValue $key): ?AbstractValue {
		return \null;
	}

	/**
	 * Returns a scalar value to be used as a hash value that can be used as
	 * scalar key for PHP arrays used in Primi internals.
	 *
	 * @throws UnhashableTypeException
	 */
	public function hash(): string {
		throw new UnhashableTypeException(static::TYPE);
	}

}
