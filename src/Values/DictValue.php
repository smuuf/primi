<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Helpers\Stats;
use \Smuuf\Primi\Ex\KeyError;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\CircularDetector;
use \Smuuf\Primi\Structures\MapContainer;

/**
 * @property MapContainer $value Internal map container.
 */
class DictValue extends AbstractValue {

	const TYPE = "dict";

	/**
	 * Create new instance from iterable list containing `[key, value]` Primi
	 * value tuples.
	 */
	public function __construct(iterable $tuples = []) {
		$this->value = MapContainer::fromTuples($tuples);
		Stats::add('values_dict');
	}

	public function __clone() {
		$this->value = clone $this->value;
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

	public function getLength(): ?int {
		return $this->value->count();
	}

	public function isTruthy(): bool {
		return (bool) $this->value->count();
	}

	private static function convertToString($self, CircularDetector $cd): string {

		// Track current value object with circular detector.
		$cd->add(\spl_object_hash($self));

		$return = "{";
		foreach ($self->value as $key => $item) {

			// This avoids infinite loops with self-nested structures by
			// checking whether circular detector determined that we
			// would end up going in (infinite) circles.
			$hash = \spl_object_hash($item);
			$str = $cd->has($hash)
				? \sprintf("*recursion (%s)*", Func::object_hash($item))
				: $item->getStringRepr($cd);

			$return .= \sprintf("%s: %s, ", $key->getStringRepr(), $str);

		}

		return \rtrim($return, ', ') . "}";

	}

	/**
	 * @returns \Iterator<string, AbstractValue>
	 */
	public function getIterator(): \Iterator {
		return $this->value->getIterator();
	}

	public function itemGet(AbstractValue $key): AbstractValue {

		if (!$this->value->hasKey($key)) {
			throw new KeyError($key->getStringRepr());
		}

		return $this->value[$key];

	}

	public function itemSet(?AbstractValue $key, AbstractValue $value): bool {

		if ($key === \null) {
			throw new RuntimeError("Must specify key when inserting into dict.");
		}

		$this->value[$key] = $value;
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
