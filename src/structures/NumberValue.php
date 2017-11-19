<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\ISupportsMultiplication;
use \Smuuf\Primi\ISupportsComparison;
use \Smuuf\Primi\ISupportsAddition;
use \Smuuf\Primi\ISupportsSubtraction;
use \Smuuf\Primi\ISupportsDivision;
use \Smuuf\Primi\ISupportsUnary;

class NumberValue extends Value implements
	ISupportsAddition,
	ISupportsSubtraction,
	ISupportsMultiplication,
	ISupportsDivision,
	ISupportsUnary,
	ISupportsComparison
{

	const TYPE = "number";

	public function __construct(string $value) {
		$this->value = self::isNumericInt($value) ? (int) $value : (float) $value;
	}

	public static function isNumericInt(string $input) {

		// Trim any present sign, because it screws up the detection.
		// "+1" _is_ int, but the equation below would wrongly return false, because
		// it's casted to (int) and the sign disappears there -> false.
		$input = ltrim($input, "+-");

		return (string) (int) $input === (string) $input;

	}

	public static function isNumeric(string $input): bool {
		return \preg_match('#^[+-]?\d+(\.\d+)?$#', $input);
	}

	public function doAddition(Value $rightOperand): Value {

		self::allowTypes($rightOperand, self::class, StringValue::class);

		if ($rightOperand instanceof StringValue && !self::isNumeric($rightOperand->value)) {
			return new StringValue($this->value . $rightOperand->value);
		}

		return new self($this->value + $rightOperand->value);

	}

	public function doSubtraction(Value $rightOperand): self {
		self::allowTypes($rightOperand, self::class);
		return new self($this->value - $rightOperand->value);
	}

	public function doMultiplication(Value $rightOperand): self {
		self::allowTypes($rightOperand, self::class);
		return new self($this->value * $rightOperand->value);
	}

	public function doDivision(Value $rightOperand): self {

		self::allowTypes($rightOperand, self::class);

		// Avoid division by zero.
		if ($rightOperand->value == 0) {
			throw new \ErrorException("Division by zero.");
		}

		return new self($this->value / $rightOperand->value);

	}

	public function doUnary(string $op): self {

		switch ($op) {
			case "++":
				return new self($this->value + 1);
			case "--":
				return new self($this->value - 1);
			default:
				throw new \TypeError;
		}

	}

	public function doComparison(string $op, Value $rightOperand): BoolValue {

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
				throw new \TypeError;
		}

	}

	// Methods

	public function callSqrt(): self {
		return new self(\sqrt($this->value));
	}

	public function callPow(self $power = null): self {
		return new self($this->value ** ($power === null ? 2 : $power->value));
	}

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

	public function callCeil(): self {
		return new self(\ceil($this->value));
	}

	public function callFloor(): self {
		return new self(\floor($this->value));
	}

	public function callRound(self $precision = null): self {
		return new self(\round($this->value, $precision ? $precision->value : 0));
	}

}
