<?php

declare(strict_types=1);

namespace Smuuf\Primi\Structures;

use Smuuf\Primi\Ex\UnhashableTypeException;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\StrictObject;

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
	\Countable
{

	use StrictObject;

	/** @var AbstractValue[] Storage for dict values. */
	private $values = [];

	/** @var AbstractValue[] Storage for key values. */
	private $keys = [];

	/**
	 * Create new instance from iterable list containing `[key, value]` Primi
	 * value tuples.
	 *
	 * @param TypeDef_PrimiObjectCouples $couples
	 * @return self
	 * @internal
	 */
	public static function fromCouples(iterable $couples): self {
		return new self($couples);
	}

	/**
	 * @param TypeDef_PrimiObjectCouples $couples
	 */
	private function __construct(iterable $couples = []) {
		$this->setAll($couples);
	}

	/**
	 * Returns a scalar key based on hash provided by the `key` value.
	 * This scalar key can be then used as key for internal ordinary PHP array.
	 *
	 * @throws UnhashableTypeException
	 */
	private static function buildScalarKey(AbstractValue $key): string {
		return "{$key->getTypeName()}.{$key->hash()}";
	}

	/**
	 * @param TypeDef_PrimiObjectCouples $couples
	 */
	public function setAll(iterable $couples): void {

		foreach ($couples as [$key, $value]) {
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
		return \array_key_exists(self::buildScalarKey($key), $this->values);
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
	 * @return \Generator<int, TypeDef_PrimiObjectCouple, null, void>
	 */
	public function getItemsIterator(): \Generator {

		foreach ($this->values as $scalarKey => $value) {
			yield [$this->keys[$scalarKey], $value];
		}

	}

	/**
	 * Returns a generator yielding keys and items from this container (as
	 * expected).
	 *
	 * @return \Generator<int, AbstractValue, null, void>
	 */
	public function getKeysIterator(): \Generator {
		yield from $this->keys;
	}

	/**
	 * Returns a generator yielding values from this container.
	 * @return \Generator<int, AbstractValue, null, void>
	 */
	public function getValuesIterator(): \Generator {
		yield from $this->values;
	}

	/**
	 * Returns a generator yielding keys and items from this container in
	 * reversed order.
	 *
	 * @return \Generator<int, TypeDef_PrimiObjectCouple, null, void>
	 */
	public function getReverseIterator(): \Generator {

		$reversed = \array_reverse($this->values, \true);
		foreach ($reversed as $scalarKey => $value) {
			yield [$this->keys[$scalarKey], $value];
		}

	}

}
