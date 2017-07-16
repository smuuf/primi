<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\ISupportsMultiplication;
use \Smuuf\Primi\ISupportsComparison;
use \Smuuf\Primi\ISupportsAddition;
use \Smuuf\Primi\ISupportsUnary;

use \Smuuf\Primi\UnsupportedOperationException;

class NumberValue extends Value implements
	ISupportsAddition,
	ISupportsMultiplication,
	ISupportsUnary,
{

	const TYPE = "number";

	public function __construct($value) {
		$this->value = self::isNumericInt($value) ? (int) $value : (float) $value;
	}

	public static function isNumericInt($input) {
		return (string) (int) $input === (string) $input;
	}

	public static function isNumeric($input) {
		return \preg_match('#\d+(\.\d+)?#', (string) $input);
	}

	public function doAddition(string $op, ISupportsAddition $rightOperand) {

		if ($op === "+") {

			if ($rightOperand instanceof StringValue && !self::isNumericInt($rightOperand->value)) {
				return new StringValue($this->value . $rightOperand->value);
			}

			return new self($this->value + $rightOperand->value);

		} else {

			if ($rightOperand instanceof StringValue) {
				throw new UnsupportedOperationException;
			}

			return new self($this->value - $rightOperand->value);

		}

	}

	public function doMultiplication(string $op, ISupportsMultiplication $rightOperand) {

		if ($op === "*") {
			if (self::isNumeric($this->value) && self::isNumeric($rightOperand->value)) {
				return new self($this->value * $rightOperand->value);
			}
		} else {
			if (self::isNumeric($this->value) && self::isNumeric($rightOperand->value)) {
				return new self($this->value / $rightOperand->value);
			}
		}

	}

	public function doUnary(string $op) {

		if ($op === "++") {
			return new self($this->value + 1);
		} else {
			return new self($this->value - 1);
		}

	}

}
