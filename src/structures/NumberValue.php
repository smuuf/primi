<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\ISupportsMultiplication;
use \Smuuf\Primi\ISupportsAddition;
use \Smuuf\Primi\ISupportsSubtraction;
use \Smuuf\Primi\ISupportsDivision;
use \Smuuf\Primi\ISupportsLength;

class NumberValue extends Value implements
	ISupportsAddition,
	ISupportsSubtraction,
	ISupportsMultiplication,
	ISupportsLength,
	ISupportsDivision,
{

	const TYPE = "number";

	public function __construct(string $value) {

		$this->value = self::isNumericInt($value)
			? (int) $value
			: (float) $value;

	}

	public function isTruthy(): bool {
		return (bool) $this->value;
	}

	public function getLength(): int {
		return strlen((string) $this->value);
	}

	public function getStringRepr(): string {
		return (string) $this->value;
	}

	public static function isNumericInt(string $input) {
		return \ctype_digit(\ltrim($input, "+-"));
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

	public function isEqualTo(Value $right): ?bool {

        if (!Common::isAnyOfTypes($right, NumberValue::class, BoolValue::class)) {
            return null;
        }

        return $this->value == $right->value;
	}

	public function hasRelationTo(string $operator, $right): ?bool {

		$l = $this->value;
		$r = $right->value;

		switch ($operator) {
			case ">":
				return $l > $r;
			case "<":
				return $l < $r;
			case ">=":
				return $l >= $r;
			case "<=":
				return $l <= $r;
		}

	}

}
