<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Stl\ArrayLibrary;

use \Smuuf\Primi\ISupportsIteration;
use \Smuuf\Primi\ISupportsDereference;
use \Smuuf\Primi\ISupportsInsertion;

class ArrayValue extends Value implements
	ISupportsIteration,
	ISupportsDereference,
	ISupportsInsertion
{

	const TYPE = "array";

	protected static $libraries = [
		ArrayLibrary::class,
	];

	public function __construct(array $arr) {
		$this->value = $arr;
	}

	public function __clone() {

		// ArrayValue is really a PHP array of other Primi value objects, so we need to do deep copy.
		\array_walk($this->value, function(&$item) {
			$item = clone $item;
		});

	}

	public function getStringValue(): string {
		return self::convertToString($this->value);
	}

	private static function convertToString($value): string {

		$return = "[";
		foreach ($value as $key => $item) {
			$key = is_numeric($key) ? $key : "\"$key\"";
			$return .= sprintf("%s: %s, ", $key, $item->getStringValue());
		}

		return rtrim($return, ', ') . "]";

	}

	public function getIterator(): \Iterator {
		return new \ArrayIterator($this->value);
	}

	public function dereference(Value $key) {

		// Array keys can be string and numbers.
		self::allowTypes($key, StringValue::class, NumberValue::class);
		$phpKey = $key->value;

		if (!isset($this->value[$phpKey])) {
			throw new \Smuuf\Primi\InternalUndefinedIndexException($phpKey);
		}

		return $this->value[$phpKey];

	}

	public function insert(?Value $key, Value $value): Value {

		if ($key === \null) {

			$this->value[] = $value;

		} else {

			// Array keys can be string and numbers.
			self::allowTypes($key, StringValue::class, NumberValue::class);
			$this->value[$key->value] = $value;

		}

		return $this;

	}

	public function getInsertionProxy(?Value $key): InsertionProxy {
		return new InsertionProxy($this, $key);
	}

	// Properties.

	public function propLength(): NumberValue {
		return new NumberValue((string) \count($this->value));
	}

}
