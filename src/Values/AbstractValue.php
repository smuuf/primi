<?php

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Location;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Ex\UnhashableTypeException;
use \Smuuf\Primi\Values\TypeValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Helpers\ValueFriends;
use \Smuuf\Primi\Structures\CallArgs;
use \Smuuf\Primi\Structures\FnContainer;

/**
 * Primi value == Primi object in our case.
 */
abstract class AbstractValue extends ValueFriends {

	/** @const string Name of Primi (object) type. */
	protected const TYPE = '__undefined__';

	/** Attributes of Primi object. */
	protected array $attrs = [];

	/**
	 * Take any PHP value and convert it into a Primi value object of an
	 * appropriate type.
	 *
	 * NOTE: We're not checking \is_callable on bare $value, because for example
	 * string 'time' would be determined to be the PHP's 'time' function and
	 * we do not want that (and it would also be a security issue).
	 */
	public static function buildAuto($value) {

		switch (\true) {
			case $value === \null:
				return Interned::null();
			case \is_bool($value):
				return Interned::bool($value);
			case \is_int($value) || \is_float($value) || \is_numeric($value):
				return Interned::number(Func::scientific_to_decimal((string) $value));
			case $value instanceof \Closure;
				return new FuncValue(FnContainer::buildFromClosure($value));
			case \is_array($value):
				if (\is_callable($value)) {
					return new FuncValue(FnContainer::buildFromClosure($value));
				}

				$inner = \array_map(__METHOD__, $value);
				if (!array_is_list($value)) {
					return new DictValue(Func::array_to_primi_value_tuples($inner));
				}
				return new ListValue($inner);

			case $value instanceof AbstractValue:
				return $value;
			default:
				return Interned::string((string) $value);
		}

	}

	/**
	 * Returns the core PHP value of this Primi value object.
	 */
	final public function getInternalValue() {
		return $this->value;
	}

	/**
	 * Returns an unambiguous string representation of internal value.
	 *
	 * If possible, is should be in such form that it the result of this
	 * method can be used as Primi source code to recreate that value.
	 */
	abstract public function getStringRepr(): string;

	/**
	 * Returns a string representation of value.
	 */
	public function getStringValue(): string {
		return $this->getStringRepr();
	}

	/**
	 * Returns dict array with this all attributes of this value.
	 */
	final public function getAttrs(): array {
		return $this->attrs;
	}

	/**
	 * Return the Primi type object this Primi value is instance of.
	 */
	abstract public function getType(): TypeValue;

	/**
	 * Return the name of Primi type of this value as string.
	 */
	public function getTypeName(): string {
		return $this->getType()->getName();
	}

	// Length.

	/**
	 * Values can report the length of it (i.e. its internal value).
	 * Values without any meaningful length can report null (default).
	 */
	public function getLength(): ?int {
		return \null;
	}

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
	 * If the value is not callable, throws TypeError.
	 *
	 * This API differs from "return null when unsupported" used elsewhere,
	 * because then someone would always have to check if the returned value
	 * was a PHP null, to find out the value does not support invocation. Every
	 * time, after each invocation (which doesn't even make much sense).
	 *
	 * This way the exception is just simply thrown once.
	 *
	 * @param Context $context Runtime context of the call-site.
	 * @param ?CallArgs $args Args object with call arguments (optional).
	 * @param ?Location $callsite Call site location (optional).
	 * @throws TypeError
	 */
	public function invoke(
		Context $context,
		?CallArgs $args = \null,
		?Location $callsite = \null
	): ?AbstractValue {
		throw new TypeError("'{$this->getTypeName()}' object is not callable");
	}

	public function getIterator(): ?\Iterator {
		return \null;
	}

	/**
	 * Assign a value under specified key into this value.
	 *
	 * Must return `true` on successful assignment, or `false` if assignment is
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
	 * Assign an attr to the value.
	 *
	 * Must return true on successful assignment, or `false` if assignment is
	 * not supported.
	 *
	 * NOTE: This attribute name can only be strings, so there's no need to
	 * accept StringValue as $key.
	 */
	public function attrSet(string $name, AbstractValue $value): bool {
		return \false;
	}

	/**
	 * Returns an attr from the value.
	 *
	 * This must return either a value object (which is an attribute of this
	 * value object) or `null`, if not found.
	 *
	 * If this returns `null`, object hierarchy will be traversed upwards and
	 * attr will be searched in the parent object.
	 *
	 * This API is differs from, for example, `self::itemGet()`, as `null` does
	 * NOT represent an "unsupported" operation, but rather "it's not here, try
	 * elsewhere".
	 *
	 * As the above is expected to be the most common thing to do, unsupported
	 * attr access instead should throw a RuntimeError.
	 *
	 * NOTE: This attribute name can only be strings, so there's no need to
	 * accept StringValue as $key.
	 */
	public function attrGet(string $name): ?AbstractValue {
		return $this->attrs[$name]
			?? Func::attr_lookup_type_hierarchy($this->getType(), $name, $this);
	}

	/**
	 * Return attribute from this Primi object - without any inheritance or
	 * type shenanigans.
	 *
	 * For internal usage by Primi engine - keeping the $attr property of
	 * PHP AbstractValue class protected.
	 *
	 * @internal
	 */
	public function rawAttrGet(string $name): ?AbstractValue {
		return $this->attrs[$name] ?? \null;
	}

	/**
	 * Returns a scalar value to be used as a hash value that can be used as
	 * scalar key for PHP arrays used in Primi internals.
	 *
	 * @throws UnhashableTypeException
	 */
	public function hash(): string {
		throw new UnhashableTypeException($this->getTypeName());
	}

	/**
	 * Return PHP array contain listing of items/attrs inside the object.
	 *
	 * This is mainly for the builtin dir() function Primi provides for
	 * easy inspection of contents of an object.
	 *
	 * @throws UnhashableTypeException
	 */
	public function dirItems(): ?array {
		return \array_keys($this->attrs);
	}

}
