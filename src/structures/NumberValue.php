<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\ISupportsMultiplication;
use \Smuuf\Primi\ISupportsAddition;
use \Smuuf\Primi\ISupportsSubtraction;
use \Smuuf\Primi\ISupportsDivision;
use \Smuuf\Primi\ISupportsLength;

use function \Smuuf\Primi\Helpers\is_any_of_types as primifn_is_any_of_types;
use function \Smuuf\Primi\Helpers\is_numeric_int as primifn_is_numeric_int;

class NumberValue extends Value implements
	ISupportsAddition,
	ISupportsSubtraction,
	ISupportsMultiplication,
	ISupportsLength,
	ISupportsDivision
{

	const TYPE = "number";

	public function __construct(string $value) {

		$this->value = primifn_is_numeric_int($value)
			? (int) $value
			: (float) $value;

	}

	public function isTruthy(): bool {
		return (bool) $this->value;
	}

	public function getLength(): int {
		return \strlen((string) $this->value);
	}

	public function getStringRepr(): string {
		return (string) $this->value;
	}

	public function doAddition(Value $right): ?Value {

		if (!$right instanceof NumberValue) {
			return \null;
		}

		return new self($this->value + $right->value);

	}

	public function doSubtraction(Value $right): ?Value {

		if (!$right instanceof NumberValue) {
			return \null;
		}

		return new self($this->value - $right->value);

	}

	public function doMultiplication(Value $right): ?Value {

		if (!$right instanceof NumberValue) {
			return \null;
		}

		return new self($this->value * $right->value);

	}

	public function doDivision(Value $right): ?Value {

		if (!$right instanceof NumberValue) {
			return \null;
		}

		// Avoid division by zero.
		if ($right->value === 0) {
			throw new RuntimeError("Division by zero");
		}

		return new self($this->value / $right->value);

	}

	public function isEqualTo(Value $right): ?bool {

		if (!primifn_is_any_of_types($right, NumberValue::class, BoolValue::class)) {
			return \null;
		}

		return $this->value == $right->value;

	}

	public function hasRelationTo(string $operator, Value $right): ?bool {

		if (!primifn_is_any_of_types($right, NumberValue::class)) {
			return \null;
		}

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
