<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Stdlib\StaticTypes;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Indices;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Helpers\CircularDetector;

class ListValue extends AbstractNativeValue {

	protected const TYPE = "list";

	public function reindex(): void {
		$this->value = \array_values($this->value);
	}

	public function __construct(array $items) {

		// Ensuring the list is indexed from 0. Keys will be ignored.
		$this->value = \array_values($items);

	}

	public function __clone() {

		// Contents of a list is internally really just a PHP array of other
		// Primi value objects, so we need to do deep copy.
		\array_walk($this->value, function(&$item) {
			$item = clone $item;
		});

	}

	public function getType(): TypeValue {
		return StaticTypes::getListType();
	}

	public function getLength(): ?int {
		return \count($this->value);
	}

	public function isTruthy(): bool {
		return (bool) $this->value;
	}

	public function getStringRepr(CircularDetector $cd = \null): string {

		// If this is a root method of getting a string value, create instance
		// of circular references detector, which we will from now on pass
		// to all deeper methods.
		if (!$cd) {
			$cd = new CircularDetector;
		}

		return self::convertToString($this, $cd);

	}

	private static function convertToString(
		self $self,
		CircularDetector $cd
	): string {

		// Track current value object with circular detector.
		$cd->add(\spl_object_hash($self));

		$return = [];
		foreach ($self->value as $item) {
			// This avoids infinite loops with self-nested structures by
			// checking whether circular detector determined that we
			// would end up going in (infinite) circles.
			$hash = \spl_object_hash($item);
			$return[] = $cd->has($hash)
				? \sprintf("*recursion (%s)*", Func::object_hash($item))
				: $item->getStringRepr($cd);

		}

		return sprintf("[%s]", \implode(', ', $return));

	}

	public function getIterator(): \Iterator {

		$index = 0; // Always index from zero with incrementing by 1.
		foreach ($this->value as $value) {
			yield Interned::number((string) ($index++)) => $value;
		}

	}

	public function itemGet(AbstractValue $index): AbstractValue {

		if (
			!$index instanceof NumberValue
			|| !Func::is_round_int($index->value)
		) {
			throw new RuntimeError("List index must be integer");
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
			throw new RuntimeError("List index must be integer");
		}

		$actualIndex = Indices::resolveIndexOrError(
			(int) $index->value,
			$this->value
		);

		$this->value[$actualIndex] = $value;
		return \true;

	}

	public function doAddition(AbstractValue $right): ?AbstractValue {

		// Lists can only be added to lists.
		if (!$right instanceof self) {
			return \null;
		}

		return new self(\array_merge($this->value, $right->value));

	}

	public function doMultiplication(AbstractValue $right): ?AbstractValue {

		// Lists can only be multiplied by a number...
		if (!$right instanceof NumberValue) {
			return \null;
		}

		// ... and that number must be an integer.
		if (!Func::is_round_int((string) $right->value)) {
			throw new RuntimeError("List can be only multiplied by an integer");
		}

		// Helper contains at least one empty array, so array_merge doesn't
		// complain about empty arguments for PHP<7.4.
		$helper = [[]];

		// Multiplying lists by an integer N returns a new list consisting of
		// the original list appended to itself N-1 times.
		$limit = $right->value;
		for ($i = 0; $i++ < $limit;) {
			$helper[] = $this->value;
		}

		// This should be efficient, since a new array (apart from the empty
		// helper) is created only once, using the splat operator on the helper,
		// which contains only references to the original array (and not copies
		// of it).
		return new self(\array_merge(...$helper));

	}

	public function isEqualTo(AbstractValue $right): ?bool {

		if (!$right instanceof ListValue) {
			return \null;
		}

		// Simple comparison of both arrays should be sufficient.
		// PHP manual describes object (which are in these arrays) comparison:
		// Two object instances are equal if they have the same attributes and
		// values (values are compared with ==).
		// See https://www.php.net/manual/en/language.oop5.object-comparison.php.
		return $this->value == $right->value;

	}

	public function doesContain(AbstractValue $right): ?bool {

		// Let's see if the needle object is in list value (which is an array of
		// Primi value objects). Non-strict search allows to match dictionaries
		// with the same key-values but in different order.
		return \in_array($right, $this->value);

	}

}
