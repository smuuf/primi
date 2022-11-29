<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Ex\KeyError;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\UnhashableTypeException;
use \Smuuf\Primi\Stdlib\BuiltinTypes;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Structures\MapContainer;

/**
 * @property MapContainer $value Internal map container.
 */
class DictValue extends AbstractBuiltinValue {

	public const TYPE = "dict";

	/**
	 * Create new instance from iterable list containing `[key, value]` Primi
	 * value tuples.
	 *
	 * @param TypeDef_PrimiObjectCouples $items
	 */
	public function __construct(iterable $items = []) {
		$this->value = MapContainer::fromCouples($items);
	}

	public function __clone() {
		$this->value = clone $this->value;
	}

	public function getType(): TypeValue {
		return BuiltinTypes::getDictType();
	}

	public function getStringRepr(): string {
		return self::convertToString($this);
	}

	public function getLength(): ?int {
		return $this->value->count();
	}

	public function isTruthy(): bool {
		return (bool) $this->value->count();
	}

	private static function convertToString(
		self $self
	): string {

		$return = [];
		foreach ($self->value->getItemsIterator() as [$k, $v]) {

			// This avoids infinite loops with self-nested structures by
			// checking whether circular detector determined that we
			// would end up going in (infinite) circles.
			$str = $v === $self
				? \sprintf("{ *recursion (%s)* }", Func::object_hash($v))
				: $v->getStringRepr();

			$return[] = \sprintf("%s: %s", $k->getStringRepr(), $str);

		}

		return sprintf("{%s}", \implode(', ', $return));

	}

	/**
	 * @return \Iterator<int, AbstractValue>
	 */
	public function getIterator(): \Iterator {
		yield from $this->value->getKeysIterator();
	}

	public function itemGet(AbstractValue $key): AbstractValue {

		try {
			if (!$this->value->hasKey($key)) {
				throw new KeyError($key->getStringRepr());
			}
		} catch (UnhashableTypeException $e) {
			throw new TypeError(\sprintf(
				"Key is of unhashable type '%s'",
				$e->getType()
			));
		}

		return $this->value->get($key);

	}

	public function itemSet(?AbstractValue $key, AbstractValue $value): bool {

		if ($key === \null) {
			throw new RuntimeError("Must specify key when inserting into dict");
		}

		try {
			$this->value->set($key, $value);
		} catch (UnhashableTypeException $e) {
			throw new TypeError(\sprintf(
				"Key is of unhashable type '%s'",
				$e->getType()
			));
		}

		return \true;

	}

	public function isEqualTo(AbstractValue $right): ?bool {

		if (!$right instanceof self) {
			return \null;
		}

		return $this->value == $right->value;

	}

	/**
	 * The 'in' operator for dictionaries looks for keys and not values.
	 */
	public function doesContain(AbstractValue $right): ?bool {

		try {
			return $this->value->hasKey($right);
		} catch (UnhashableTypeException $e) {
			throw new TypeError(\sprintf(
				"Key is of unhashable type '%s'",
				$e->getType()
			));
		}

	}

}
