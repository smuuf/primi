<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Ex\KeyError;
use \Smuuf\Primi\Ex\LookupError;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\CircularDetector;

class DictValue extends Value {

	const TYPE = "dict";

	public function __construct(array $arr) {
		$this->value = $arr;
	}

	public function __clone() {

		// DictValue is really a PHP array of other Primi value objects,
		// so we need to do deep copy.
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
				? \sprintf("*recursion (%s)*", Func::object_hash($item))
				: $item->getStringRepr($cd);

			$return .= \sprintf("%s: %s, ", $key, $str);

		}

		return \rtrim($return, ', ') . "}";

	}

	public function getIterator(): \Iterator {
		return new \ArrayIterator($this->value);
	}

	public function itemGet(Value $key): Value {

		$phpKey = $key->getInternalValue();
		if (!\is_scalar($phpKey)) {
			$type = $phpKey::TYPE;
			throw new LookupError("Cannot use '$type' for lookup.");
		}

		if (!isset($this->value[$phpKey])) {
			throw new KeyError($phpKey);
		}

		return $this->value[$phpKey];

	}

	public function itemSet(?Value $key, Value $value): bool {

		if ($key === \null) {
			throw new RuntimeError("Must specify key when inserting into dict.");
		}

		$phpKey = $key->getInternalValue();
		if (!\is_scalar($phpKey)) {
			$type = $phpKey::TYPE;
			throw new LookupError("Cannot use '$type' for lookup.");
		}

		$this->value[$phpKey] = $value;
		return true;

	}


	public function isEqualTo(Value $right): ?bool {

		if (!$right instanceof self) {
			return \null;
		}

		// Simple comparison of both arrays should be sufficient.
		// PHP manual describes object (which are in these arrays) comparison:
		// Two object instances are equal if they have the same attributes and
		// values (values are compared with ==).
		// See https://www.php.net/manual/en/language.oop5.object-comparison.php.
		return $this->value == $right->value;

	}

	/**
	 * The 'in' operator for dictionaries looks for keys and not values.
	 */
	public function doesContain(Value $right): ?bool {

		// BUG, TODO: This should  to be a strict compasion, because if it's
		// not, string key '123' would be the same as number key 123.
		// But if it _was_ strict comparison, the number key 123 wouldn't match
		// with Primi-number-value key 123, because NumberValue stores its value
		// internally as a string. This is a cunundrum and yet-to-be-solved.

		return \array_search(
			$right->value,
			\array_keys($this->value)
			// true // Strict comparison.
		) !== false;

	}

}
