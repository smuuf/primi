<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Stl\ArrayLibrary;
use \Smuuf\Primi\Helpers\CircularDetector;
use \Smuuf\Primi\Helpers;

use \Smuuf\Primi\ISupportsIteration;
use \Smuuf\Primi\ISupportsArrayAccess;

class ArrayValue extends Value implements
	ISupportsIteration,
	ISupportsArrayAccess
{

	const TYPE = "array";

	public function __construct(array $arr) {
		$this->value = $arr;
	}

	public function __clone() {

		// ArrayValue is really a PHP array of other Primi value objects, so we need to do deep copy.
		\array_walk($this->value, function(&$item) {
			$item = clone $item;
		});

	}

	public function getStringValue(CircularDetector $cd = null): string {

		// If this is a root method of getting a string value, create instance
		// of circular references detector, which we will from now on pass
		// to all deeper methods.
		if (!$cd) {
			$cd = new CircularDetector;
		}

		return self::convertToString($this, $cd);

	}

	private static function convertToString($self, CircularDetector $cd): string {

		$cd->add(spl_object_hash($self));

		$return = "[";
		foreach ($self->value as $key => $item) {

			$key = is_numeric($key) ? $key : "\"$key\"";

			// This avoids infinite loops with self-nested structures by
			// checking whether circular detector determined that we
			// would end up going in (infinite) circles.
			$hash = spl_object_hash($item);
			$str = $cd->has($hash)
				? sprintf("*recursion (%s)*", Helpers::objectHash($item))
				: $item->getStringValue($cd);

			$return .= sprintf("%s: %s, ", $key, $str);

		}

		return rtrim($return, ', ') . "]";

	}

	public function getIterator(): \Iterator {
		return new \ArrayIterator($this->value);
	}

	public function arrayGet(string $key): Value {

		if (!isset($this->value[$key])) {
			throw new \Smuuf\Primi\InternalUndefinedIndexException($key);
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

	public function getArrayInsertionProxy(?string $key): ArrayInsertionProxy {
		return new ArrayInsertionProxy($this, $key);
	}

}
