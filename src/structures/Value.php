<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\UnhashableTypeException;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Structures\ValueFriends;

abstract class Value extends ValueFriends{

	const TYPE = "any";

	public static function buildAutomatic($value) {

		switch (\true) {
			case $value === \null:
				return new NullValue;
			case \is_bool($value):
				return new BoolValue($value);
			case \is_numeric($value):
				return new NumberValue(Func::scientific_to_decimal($value));
			case \is_callable($value);
				// Must be before "is_array" case, because some "arrays"
				// can be in reality "callables".
				return new FuncValue(FnContainer::buildFromClosure($value));
			case \is_array($value):
				$inner = \array_map([self::class, 'buildAutomatic'], $value);
				if (Func::is_array_dict($value)) {
					return new DictValue(Func::php_array_to_dict_pairs($inner));
				} else {
					return new ListValue($inner);
				}
			case $value instanceof Value:
				return $value;
			default:
				return new StringValue($value);
		}

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
		Value $right
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
	public function hasRelationTo(string $operator, Value $right): ?bool {
		return \null;
	}

	public function doesContain(Value $right): ?bool {
		return \null;
	}

	public function doAddition(Value $right): ?Value {
		return \null;
	}

	public function doSubtraction(Value $right): ?Value {
		return \null;
	}

	public function doMultiplication(Value $right): ?Value {
		return \null;
	}

	public function doDivision(Value $right): ?Value {
		return \null;
	}

	public function doPower(Value $right): ?Value {
		return \null;
	}

	/**
	 * Called when used as `some_value()`.
	 *
	 * If value supports invocation, it must return a Value. Otherwise return
	 * null.
	 *
	 * @param Context $context Runtime context of the call-site.
	 * @param array<string, Value> $args Array dictionary of call arguments.
	 */
	public function invoke(
		Context $context,
		array $args = []
	): ?Value {
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
	public function itemSet(?Value $key, Value $value): bool {
		return \false;
	}

	/**
	 * Returns some internal value by specified key.
	 *
	 * Must return some value object, or `null` if such operation is not
	 * supported.
	 */
	public function itemGet(Value $key): ?Value {
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
