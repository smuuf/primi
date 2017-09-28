<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\UnsupportedOperationException;
use \Smuuf\Primi\ISupportsComparison;
use \Smuuf\Primi\ISupportsAddition;
use \Smuuf\Primi\ISupportsSubtraction;
use \Smuuf\Primi\ISupportsIteration;
use \Smuuf\Primi\ISupportsDereference;
use \Smuuf\Primi\ISupportsInsertion;

class StringValue extends Value implements
	ISupportsAddition,
	ISupportsSubtraction,
	ISupportsIteration,
	ISupportsComparison,
	ISupportsInsertion,
	ISupportsDereference
{

	const TYPE = "string";
	protected $splitCache;

	public function __construct(string $value) {
		$this->value = self::expandSequences($value);
	}

	public function doAddition(Value $rightOperand) {
		return new self($this->value . $rightOperand->value);
	}

	public function doSubtraction(Value $rightOperand) {

		if ($rightOperand instanceof NumberValue) {
			throw new UnsupportedOperationException;
		}

		if ($rightOperand instanceof RegexValue) {
			return new self(\preg_replace($rightOperand->value, null, $this->value));
		}

		return new self(\str_replace($rightOperand->value, null, $this->value));

	}

	public function doComparison(string $op, Value $rightOperand) {

		switch ($op) {
			case "==":

				if ($rightOperand instanceof RegexValue) {
					$result = \preg_match($rightOperand->value, $this->value);
				} else {
					$result = $this->value === $rightOperand->value;
				}

				return new BoolValue($result);

			case "!=":
				return new BoolValue($this->value !== $rightOperand->value);
			default:
				throw new UnsupportedOperationException;
		}

	}

	public function dereference(Value $index) {

		$phpIndex = (string) $index->value;

		if (!isset($this->value[$phpIndex])) {
			throw new \Smuuf\Primi\ErrorException("Undefined index '$phpIndex'");
		}

		return $this->value[$phpIndex];

	}

	public function insert(string $key, Value $value) {

		if (!$value instanceof self) {
			throw new \TypeError;
		}

		if ($key === "") {
			$this->value .= $value->value;
		} else {
			$this->value[$key] = $value->value;
		}
	}

	public function getInsertionProxy(string $key): InsertionProxy {
		return new InsertionProxy($this, $key);
	}

	public function getIterator(): \Iterator {
		return $this->splitCache ?: $this->splitCache = self::utfSplit($this->value);
	}

	// Helpers

	protected static function expandSequences(string $string) {

		// Primi strings support some escape sequences.
		$string = \str_replace('\n', "\n", $string);
		return $string;

	}

	private static function utfSplit(string $string) {
		$strlen = \mb_strlen($string);
		while ($strlen) {
			yield new self(\mb_substr($string, 0, 1, "UTF-8"));
			$string = \mb_substr($string, 1, $strlen, "UTF-8");
			$strlen = \mb_strlen($string);
		}
	}

	// Methods

	public function callReplace(Value $search, self $replace = null): self {

		// Replacing using array of search-replace pairs.
		if ($search instanceof ArrayValue) {

			$from = \array_keys($search->value);

			// Values in ArrayValues are stored as Value objects, so we need to extract the real PHP values from it.
			$to = \array_values(\array_map(function($item) {
				return $item->value;
			}, $search->value));

			return new self(\str_replace($from, $to, $this->value));

		}

		if ($replace === null) {
			throw new \TypeError;
		}

		if ($search instanceof self || $search instanceof NumberValue) {

			// Handle both string/number values the same way.
			return new self(\str_replace((string) $search->value, $replace->value, $this->value));

		} elseif ($search instanceof RegexValue) {
			return new self(\preg_replace($search->value, $replace->value, $this->value));
		} else {
			throw new \TypeError;
		}

	}

	public function callFirst(Value $needle): NumberValue {

		if (!$needle instanceof self && !$needle instanceof NumberValue) {
			throw new \TypeError;
		}

		$pos = mb_strpos($this->value, (string) $needle->value);
		return new NumberValue($pos === false ? -1 : $pos);

	}

	public function callLast(Value $needle): NumberValue {

		if (!$needle instanceof self && !$needle instanceof NumberValue) {
			throw new \TypeError;
		}

		$pos = mb_strrpos($this->value, (string) $needle->value);
		return new NumberValue($pos === false ? -1 : $pos);

	}

}
