<?php

declare(strict_types=1);

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Ex\UnhashableTypeException;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\StrictObject;

/**
 * Map container structure supporting Primi value objects as keys.
 *
 * Slightly simpler and faster than php-ds or its polyfill, which both seem
 * slower than this. (Or I made a mistake when measuring its performance.)
 *
 * Also these map containers can be compared with ordinary == operator (at least
 * it seems so - until someone finds a problem with it).
 *
 * @internal
 */
class MapContainer implements
	\ArrayAccess,
	\IteratorAggregate,
	\Countable
{

	use StrictObject;

	/** @var AbstractValue[] Storage for dict values. */
	private $values = [];

	/** @var AbstractValue[] Storage for key values. */
	private $keys = [];

	/**
	 * Create new instance from iterable that already returns Primi values as
	 * both keys and values.
	 *
	 * @internal
	 */
	public static function fromMap(iterable $map) {
		return new self(Func::iterator_as_tuples($map));
	}

	/**
	 * Create new instance from iterable list containing `[key, value]` Primi
	 * value tuples.
	 *
	 * @internal
	 */
	public static function fromTuples(iterable $pairs) {
		return new self($pairs);
	}

	private function __construct(iterable $pairs = []) {
		$this->setAll($pairs);
	}

	/**
	 * Returns a scalar key based on hash provided by the `key` value.
	 * This scalar key can be then used as key for internal ordinady PHP array.
	 *
	 * @throws UnhashableTypeException
	 */
	private static function buildScalarKey(AbstractValue $key): string {
		return $key::TYPE . ".{$key->hash()}";
	}

	/**
	 * @param iterable<AbstractValue, AbstractValue> $pairs
	 */
	public function setAll(iterable $pairs): void {

		foreach ($pairs as [$key, $value]) {
			$scalarKey = self::buildScalarKey($key);
			$this->values[$scalarKey] = $value;
			$this->keys[$scalarKey] = $key;
		}

	}

	/**
	 * Sets a `key: value` pair to the map.
	 *
	 * @throws UnhashableTypeException
	 */
	public function set(AbstractValue $key, AbstractValue $value): void {

		$scalarKey = self::buildScalarKey($key);
		$this->values[$scalarKey] = $value;
		$this->keys[$scalarKey] = $key;

	}

	/**
	 * Return `true` if this key is present in the map. Return `false`
	 * otherwise.
	 *
	 * @throws UnhashableTypeException
	 */
	public function hasKey(AbstractValue $key): bool {
		return isset($this->values[self::buildScalarKey($key)]);
	}

	/**
	 * Returns the first `key` value found under which this `value` is stored,
	 * or `null` if not found at all.
	 */
	public function findValue(AbstractValue $value): ?AbstractValue {

		// Non-strict on purpose, so that different value instances having
		// the same internal value are considered to be equal.
		$scalarKey = \array_search($value, $this->values);
		if ($scalarKey === \false) {
			return \null;
		}

		return $this->keys[$scalarKey];

	}

	/**
	 * Removes `key: value` pair from the map, based on the `key`.
	 *
	 * @throws UnhashableTypeException
	 */
	public function remove(AbstractValue $key): void {

		$scalarKey = self::buildScalarKey($key);
		unset($this->values[$scalarKey]);
		unset($this->keys[$scalarKey]);

	}

	public function get(AbstractValue $key): ?AbstractValue {
		return $this->values[self::buildScalarKey($key)] ?? \null;
	}

	/**
	 * Return number of items in the map.
	 */
	public function count(): int {
		return \count($this->values);
	}

	/**
	 * Returns a generator yielding keys and items from this container (as
	 * expected).
	 *
	 * @returns \Generator<string, AbstractValue, null, null>
	 */
	public function getIterator(): \Generator {

		foreach ($this->values as $scalarKey => $value) {
			yield $this->keys[$scalarKey] => $value;
		}

	}

	/**
	 * Returns a generator yielding keys and items from this container in
	 * reversed order.
	 *
	 * @returns \Generator<string, AbstractValue, null, null>
	 */
	public function getReverseIterator(): \Generator {

		$reversed = \array_reverse($this->values, \true);
		foreach ($reversed as $scalarKey => $value) {
			yield $this->keys[$scalarKey] => $value;
		}

	}

	// ArrayAccess implementation.

	public function offsetExists($key): bool {
		return $this->hasKey($key);
	}

	public function offsetGet($key) {
		return $this->get($key);
	}

	public function offsetSet($key, $value): void {
		$this->set($key, $value);
	}

	public function offsetUnset($key) {
		$this->remove($key);
	}

}
