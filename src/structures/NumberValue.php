<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\ISupportsMultiplication;
use \Smuuf\Primi\ISupportsComparison;
use \Smuuf\Primi\ISupportsAddition;
use \Smuuf\Primi\ISupportsSubtraction;
use \Smuuf\Primi\ISupportsDivision;
use \Smuuf\Primi\ISupportsUnary;

use \Smuuf\Primi\UnsupportedOperationException;

class NumberValue extends Value implements
	ISupportsAddition,
	ISupportsSubtraction,
	ISupportsMultiplication,
	ISupportsDivision,
	ISupportsUnary,
	ISupportsComparison
{

	const TYPE = "number";

	public function __construct($value) {
		$this->value = self::isNumericInt($value) ? (int) $value : (float) $value;
	}

	public static function isNumericInt($input) {
		return (string) (int) $input === (string) $input;
	}

	public static function isNumeric($input) {
		return \preg_match('#^\d+(\.\d+)?$#', (string) $input);
	}

	public function doAddition(ISupportsAddition $rightOperand) {

		if ($rightOperand instanceof StringValue && !self::isNumericInt($rightOperand->value)) {
			return new StringValue($this->value . $rightOperand->value);
		}

		return new self($this->value + $rightOperand->value);

	}

	public function doSubtraction(ISupportsSubtraction $rightOperand) {

		if ($rightOperand instanceof StringValue) {
			throw new UnsupportedOperationException;
		}

		return new self($this->value - $rightOperand->value);

	}

	public function doMultiplication(ISupportsMultiplication $rightOperand) {

		if (!$rightOperand instanceof self) {
			throw new UnsupportedOperationException;
		}

		return new self($this->value * $rightOperand->value);

	}

	public function doDivision(ISupportsDivision $rightOperand) {

		if (!$rightOperand instanceof self) {
			throw new UnsupportedOperationException;
		}

		return new self($this->value / $rightOperand->value);

	}

	public function doUnary(string $op) {

		if ($op === "++") {
			return new self($this->value + 1);
		} else {
			return new self($this->value - 1);
		}

	}

	public function doComparison(string $op, ISupportsComparison $rightOperand) {

		switch ($op) {
			case "==":
				return new BoolValue($this->value == $rightOperand->getPhpValue());
			case "!=":
				return new BoolValue($this->value != $rightOperand->getPhpValue());
			case ">":
				return new BoolValue($this->value > $rightOperand->getPhpValue());
			case "<":
				return new BoolValue($this->value < $rightOperand->getPhpValue());
			case ">=":
				return new BoolValue($this->value >= $rightOperand->getPhpValue());
			case "<=":
				return new BoolValue($this->value <= $rightOperand->getPhpValue());
			default:
				throw new UnsupportedOperationException;
		}

	}

}
