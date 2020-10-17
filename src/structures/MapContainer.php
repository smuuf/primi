<?php

declare(strict_types=1);

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Ex\UnhashableTypeException;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Structures\Value;

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
class MapContainer extends \Smuuf\Primi\StrictObject implements
	\ArrayAccess,
	\IteratorAggregate,
	\Countable
{

	private $values = [];
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
	private static function buildScalarKey(Value $key): string {
		return $key::TYPE . ".{$key->hash()}";
	}

	public function setAll($pairs): void {

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
	public function set(Value $key, Value $value): void {

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
	public function hasKey(Value $key): bool {
		return isset($this->values[self::buildScalarKey($key)]);
	}

	/**
	 * Returns the first `key` value found under which this `value` is stored,
	 * or `null` if not found at all.
	 */
	public function findValue(Value $value): ?Value {

		// Non-strict on purpose, so that different value instances having
		// the same internal value are considered to be equal.
		$scalarKey = \array_search($value, $this->values);
		if ($scalarKey === false) {
			return null;
		}

		return $this->keys[$scalarKey];

	}

	/**
	 * Removes `key: value` pair from the map, based on the `key`.
	 *
	 * @throws UnhashableTypeException
	 */
	public function remove(Value $key): void {

		$scalarKey = self::buildScalarKey($key);
		unset($this->values[$scalarKey]);
		unset($this->keys[$scalarKey]);

	}

	public function get(Value $key): ?Value {
		return $this->values[self::buildScalarKey($key)] ?? null;
	}

	/**
	 * Return number of items in the map.
	 */
	public function count(): int {
		return count($this->values);
	}

	public function getIterator(): \Generator {

		foreach ($this->values as $scalarKey => $value) {
			yield $this->keys[$scalarKey] => $value;
		}

	}

	public function getReverseIterator(): \Generator {

		$reversed = array_reverse($this->values, true);
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
