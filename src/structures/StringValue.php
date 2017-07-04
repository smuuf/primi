<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\UnsupportedOperationException;
use \Smuuf\Primi\ISupportsMultiplication;
use \Smuuf\Primi\ISupportsAddition;
use \Smuuf\Primi\ISupportsIteration;

class StringValue extends Value implements ISupportsAddition, ISupportsIteration, ISupportsMultiplication {

	const TYPE = "string";
	protected $splitCache;

	public function __construct(string $value) {
		$this->value = self::expandSequences($value);
	}

	public function doAddition(string $op, ISupportsAddition $operand) {

		if ($op === "+") {

			if ($operand instanceof NumberValue && NumberValue::isNumericInt($this->value)) {
				return Value::build(Value::TYPE_NUMBER, $this->value + $operand->value);
			}

			return new self($this->value . $operand->value);

		} else {

			if ($operand instanceof NumberValue) {
				throw new UnsupportedOperationException;
			}

			return new self(str_replace($operand->value, null, $this->value));

		}

	}

	public function doMultiplication(string $op, ISupportsMultiplication $operand) {

		if ($op === "*") {
			if (NumberValue::isNumeric($this->value) && NumberValue::isNumeric($operand->value)) {
				return new NumberValue($this->value * $operand->value);
			}
		} else {
			if (NumberValue::isNumeric($this->value) && NumberValue::isNumeric($operand->value)) {
				return new NumberValue($this->value / $operand->value);
			}
		}

		throw new UnsupportedOperationException;

	}

	public function getIterator() {
		return $this->splitCache ?: $this->splitCache = self::utfSplit($this->value);
	}

	// Helpers

	protected static function expandSequences(string $string) {

		// Primi strings support some escape sequences.
		$string = str_replace('\n', "\n", $string);
		return $string;

	}


	private static function utfSplit($string) {
		$strlen = mb_strlen($string);
		while ($strlen) {
			yield new self(mb_substr($string, 0, 1, "UTF-8"));
			$string = mb_substr($string, 1, $strlen, "UTF-8");
			$strlen = mb_strlen($string);
		}
	}

}
