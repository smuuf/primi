<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Ex\KeyError;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Stdlib\StaticTypes;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Structures\MapContainer;

/**
 * @property MapContainer $value Internal map container.
 */
class DictValue extends AbstractNativeValue {

	protected const TYPE = "dict";

	/**
	 * Create new instance from iterable list containing `[key, value]` Primi
	 * value tuples.
	 */
	public function __construct(iterable $items = []) {
		$this->value = MapContainer::fromTuples($items);
	}

	public function __clone() {
		$this->value = clone $this->value;
	}

	public function getType(): TypeValue {
		return StaticTypes::getDictType();
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
	 * @returns \Iterator<string, AbstractValue>
	 */
	public function getIterator(): \Iterator {
		yield from $this->value->getKeysIterator();
	}

	public function itemGet(AbstractValue $key): AbstractValue {

		if (!$this->value->hasKey($key)) {
			throw new KeyError($key->getStringRepr());
		}

		return $this->value->get($key);

	}

	public function itemSet(?AbstractValue $key, AbstractValue $value): bool {

		if ($key === \null) {
			throw new RuntimeError("Must specify key when inserting into dict");
		}

		$this->value->set($key, $value);
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
		return $this->value->hasKey($right);
	}

}
