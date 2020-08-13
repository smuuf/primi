<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Ex\KeyError;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\ISupportsLength;
use \Smuuf\Primi\ISupportsIteration;

use \Smuuf\Primi\ISupportsKeyAccess;
use \Smuuf\Primi\Helpers\CircularDetector;

class DictValue extends Value implements
	ISupportsIteration,
	ISupportsKeyAccess,
	ISupportsLength
{

	const TYPE = "dict";

	public function __construct(array $arr) {
		$this->value = $arr;
	}

	public function __clone() {

		// DictValue is really a PHP array of other Primi value objects, so we need to do deep copy.
		\array_walk($this->value, function(&$item) {
			$item = clone $item;
		});

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

	public function getLength(): int {
		return \count($this->value);
	}

	public function isTruthy(): bool {
		return (bool) $this->value;
	}

	private static function convertToString($self, CircularDetector $cd): string {

		// Track current value object with circular detector.
		$cd->add(\spl_object_hash($self));

		$return = "{";
		foreach ($self->value as $key => $item) {

			$key = \is_numeric($key) ? $key : "\"$key\"";

			// This avoids infinite loops with self-nested structures by
			// checking whether circular detector determined that we
			// would end up going in (infinite) circles.
			$hash = \spl_object_hash($item);
			$str = $cd->has($hash)
				? \sprintf("*recursion (%s)*", Common::objectHash($item))
				: $item->getStringRepr($cd);

			$return .= \sprintf("%s: %s, ", $key, $str);

		}

		return \rtrim($return, ', ') . "}";

	}

	public function getIterator(): \Iterator {
		return new \ArrayIterator($this->value);
	}

	public function arrayGet(string $key): Value {

		if (!isset($this->value[$key])) {
			throw new KeyError($key);
		}

		return $this->value[$key];

	}

	public function arraySet(?string $key, Value $value) {

		if ($key === \null) {
			$this->value[] = $value;
		} else {
			$this->value[$key] = $value;
		}

	}

	public function getInsertionProxy(?string $key): InsertionProxy {
		return new InsertionProxy($this, $key);
	}

	public function isEqualTo(Value $right): ?bool {

		if (!$right instanceof self) {
			return null;
		}

		// Simple comparison of both arrays should be sufficient.
		// PHP manual describes object (which are in these arrays) comparison:
		// Two object instances are equal if they have the same attributes and
		// values (values are compared with ==).
		// See https://www.php.net/manual/en/language.oop5.object-comparison.php.
		return $this->value == $right->value;

	}

}
