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

	public function doAddition(Value $rightOperand) {

		if ($rightOperand instanceof StringValue && !self::isNumericInt($rightOperand->value)) {
			return new StringValue($this->value . $rightOperand->value);
		}

		return new self($this->value + $rightOperand->value);

	}

	public function doSubtraction(Value $rightOperand) {

		if ($rightOperand instanceof StringValue) {
			throw new UnsupportedOperationException;
		}

		return new self($this->value - $rightOperand->value);

	}

	public function doMultiplication(Value $rightOperand) {

		if (!$rightOperand instanceof self) {
			throw new UnsupportedOperationException;
		}

		return new self($this->value * $rightOperand->value);

	}

	public function doDivision(Value $rightOperand) {

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

	public function doComparison(string $op, Value $rightOperand) {

		switch ($op) {
			case "==":
				return new BoolValue($this->value == $rightOperand->value);
			case "!=":
				return new BoolValue($this->value != $rightOperand->value);
			case ">":
				return new BoolValue($this->value > $rightOperand->value);
			case "<":
				return new BoolValue($this->value < $rightOperand->value);
			case ">=":
				return new BoolValue($this->value >= $rightOperand->value);
			case "<=":
				return new BoolValue($this->value <= $rightOperand->value);
			default:
				throw new UnsupportedOperationException;
		}

	}

	// Methods

	public function callSin(): self {
		return new self(\sin($this->value));
	}

	public function callCos(): self {
		return new self(\cos($this->value));
	}

	public function callTan(): self {
		return new self(\tan($this->value));
	}

	public function callAtan(): self {
		return new self(\atan($this->value));
	}

	public function callRound(Value $precision): self {

		if (!$precision instanceof NumberValue) {
			throw new \TypeError;
		}

		return new self(\round($this->value, $precision->value));

	}

}
