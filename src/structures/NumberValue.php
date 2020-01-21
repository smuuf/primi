<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\ISupportsMultiplication;
use \Smuuf\Primi\ISupportsComparison;
use \Smuuf\Primi\ISupportsAddition;
use \Smuuf\Primi\ISupportsSubtraction;
use \Smuuf\Primi\ISupportsDivision;

class NumberValue extends Value implements
	ISupportsAddition,
	ISupportsSubtraction,
	ISupportsMultiplication,
	ISupportsDivision,
	ISupportsComparison
{

	const TYPE = "number";

	public function __construct(string $value) {

		$this->value = self::isNumericInt($value)
			? (int) $value
			: (float) $value;

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
		return (bool) \preg_match('#^[+-]?\d+(\.\d+)?$#', $input);
	}

	public function doAddition(Value $right): Value {

		Common::allowTypes($right, self::class);
		return new self($this->value + $right->value);

	}

	public function doSubtraction(Value $right): self {
		Common::allowTypes($right, self::class);
		return new self($this->value - $right->value);
	}

	public function doMultiplication(Value $right) {

		Common::allowTypes($right, self::class, StringValue::class);

		if ($right instanceof StringValue) {
			$multiplier = $this->value;
			if (\is_int($multiplier) && $multiplier >= 0) {
				$new = \str_repeat($right->value, $multiplier);
				return new StringValue($new);
			}
			throw new \TypeError;
		}

		return new self($this->value * $right->value);

	}

	public function doDivision(Value $right): self {

		Common::allowTypes($right, self::class);

		// Avoid division by zero.
		if ($right->value === 0) {
			throw new \Smuuf\Primi\ErrorException("Division by zero");
		}

		return new self($this->value / $right->value);

	}

	public function doComparison(string $op, Value $right): BoolValue {

		Common::allowTypes(
			$right,
			self::class,
			BoolValue::class,
			StringValue::class
		);

		// Numbers and strings can only be compared for equality - never equal.
		if ($right instanceof StringValue) {
			switch ($op) {
				case "==":
					return new BoolValue(false);
				case "!=":
					return new BoolValue(true);
			}
			throw new \TypeError;
		}

		// Numbers and bools can only be compared for equality.
		if ($right instanceof BoolValue) {
			$leftTruth = Common::isTruthy($this);
			switch ($op) {
				case "==":
					return new BoolValue($leftTruth === $right->value);
				case "!=":
					return new BoolValue($leftTruth !== $right->value);
			}
			throw new \TypeError;
		}

		$l = $this->value;
		$r = $right->value;

		switch ($op) {
			case "==":
				// Don't do strict comparison - it's wrong for floats and ints.
				return new BoolValue($l == $r);
			case "!=":
				// Don't do strict comparison - it's wrong for floats and ints.
				return new BoolValue($l != $r);
			case ">":
				return new BoolValue($l > $r);
			case "<":
				return new BoolValue($l < $r);
			case ">=":
				return new BoolValue($l >= $r);
			case "<=":
				return new BoolValue($l <= $r);
			default:
				throw new \TypeError;
		}

	}

}
