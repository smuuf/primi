<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Stdlib\StaticTypes;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Indices;

/**
 * @property array<AbstractValue> $value Internal tuple container.
 */
class TupleValue extends AbstractNativeValue {

	public const TYPE = "tuple";

	/**
	 * Computing hash can be expensive, so for this immutable type, let's
	 * memoize it, because it won't (should not) change.
	 */
	private ?string $savedHash = \null;

	/**
	 * Create new instance from iterable list containing `[key, value]` Primi
	 * value tuples.
	 *
	 * @param array<AbstractValue> $items
	 */
	public function __construct(array $items = []) {

		// Let's make sure the internal array is indexed from zero.
		$this->value = \array_values($items);

	}

	public function __clone() {

		// Contents of a tuple is internally really just a PHP array of other
		// Primi value objects, so we need to do deep copy.
		\array_walk($this->value, function(&$item) {
			$item = clone $item;
		});

	}

	public function getType(): TypeValue {
		return StaticTypes::getTupleType();
	}

	public function getStringRepr(): string {
		return self::convertToString($this);
	}

	private static function convertToString(
		self $self
	): string {

		$return = [];
		foreach ($self->value as $item) {

			// This avoids infinite loops with self-nested structures by
			// checking whether circular detector determined that we
			// would end up going in (infinite) circles.
			$return[] = $item === $self
				? \sprintf("( *recursion (%s)* )", Func::object_hash($item))
				: $item->getStringRepr();

		}

		return \sprintf("(%s)", \implode(', ', $return));

	}

	public function hash(): string {

		if ($this->savedHash !== \null) {
			return $this->savedHash;
		}

		return $this->savedHash = \array_reduce(
			$this->value,
			fn($c, $i) => \md5("{$c},{$i->hash()}"),
			''
		);
	}

	public function getLength(): ?int {
		return \count($this->value);
	}

	public function isTruthy(): bool {
		return (bool) $this->value;
	}

	/**
	 * @return \Iterator<int, AbstractValue>
	 */
	public function getIterator(): \Iterator {
		yield from $this->value;
	}

	public function itemGet(AbstractValue $index): AbstractValue {

		if (
			!$index instanceof NumberValue
			|| !Func::is_round_int($index->value)
		) {
			throw new RuntimeError("Tuple index must be integer");
		}

		$actualIndex = Indices::resolveIndexOrError(
			(int) $index->value,
			$this->value
		);

		// Numbers are internally stored as strings, so get it as PHP integer.
		return $this->value[$actualIndex];

	}

	public function itemSet(?AbstractValue $index, AbstractValue $value): bool {

		if ($index === \null) {
			$this->value[] = $value;
			return \true;
		}

		if (
			!$index instanceof NumberValue
			|| !Func::is_round_int($index->value)
		) {
			throw new RuntimeError("Tuple index must be integer");
		}

		$actualIndex = Indices::resolveIndexOrError(
			(int) $index->value,
			$this->value
		);

		$this->value[$actualIndex] = $value;
		return \true;

	}

	public function isEqualTo(AbstractValue $right): ?bool {

		if (!$right instanceof self) {
			return \null;
		}

		return $this->value == $right->value;

	}

	public function doesContain(AbstractValue $right): ?bool {

		// Let's see if the needle object is in tuple value (which is internally
		// an array of Primi value objects).
		return \in_array($right, $this->value);

	}

}
