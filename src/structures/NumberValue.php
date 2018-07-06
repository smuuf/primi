<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Helpers\Common;
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

	public function getStringValue(): string {
		return (string) $this->value;
	}

	public static function isNumericInt(string $input) {

		// Trim any present sign, because it screws up the detection.
		// "+1" _is_ int, but the equation below would wrongly return false,
		// because it's casted to (int) and the sign disappears there -> false.
		$input = \ltrim($input, "+-");

		// The same with zeroes at the beginning.
		// But only if the input is not a zero.
		$input = $input !== "0" ? \ltrim($input, "0") : $input;

		return (string) (int) $input === (string) $input;

	}

	public static function isNumeric(string $input): bool {
		return
			(bool) \preg_match('#^[+-]?\d+(\.\d+)?$#', $input)
			&& (int) $input !== PHP_INT_MAX
			&& (int) $input !== PHP_INT_MIN;
	}

	public function doAddition(Value $rightOperand): Value {

		Common::allowTypes($rightOperand, self::class, StringValue::class);

		if ($rightOperand instanceof StringValue && !self::isNumeric($rightOperand->value)) {
			return new StringValue($this->value . $rightOperand->value);
		}

		return new self($this->value + $rightOperand->value);

	}

	public function doSubtraction(Value $rightOperand): self {
		Common::allowTypes($rightOperand, self::class);
		return new self($this->value - $rightOperand->value);
	}

	public function doMultiplication(Value $rightOperand) {

		Common::allowTypes($rightOperand, self::class, StringValue::class);

		if ($rightOperand instanceof StringValue) {
			$multiplier = $this->value;
			if (is_int($multiplier) && $multiplier >= 0) {
				return new StringValue(str_repeat($rightOperand->value, $multiplier));
			}
			throw new \TypeError;
		}

		return new self($this->value * $rightOperand->value);

	}

	public function doDivision(Value $rightOperand): self {

		Common::allowTypes($rightOperand, self::class);

		// Avoid division by zero.
		if ($rightOperand->value == 0) {
			throw new \Smuuf\Primi\ErrorException("Division by zero.");
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

		Common::allowTypes($rightOperand, self::class, StringValue::class);

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

}
