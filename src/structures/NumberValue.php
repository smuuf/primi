<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\ISupportsLength;
use \Smuuf\Primi\ISupportsAddition;
use \Smuuf\Primi\ISupportsDivision;
use \Smuuf\Primi\ISupportsSubtraction;
use \Smuuf\Primi\ISupportsMultiplication;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Helpers\Func;

class NumberValue extends Value implements
	ISupportsAddition,
	ISupportsSubtraction,
	ISupportsMultiplication,
	ISupportsLength,
	ISupportsDivision
{

	const TYPE = "number";

	public function __construct(string $value) {

		$this->value = Func::is_numeric_int($value)
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

		if (!Func::is_any_of_types($right, NumberValue::class, BoolValue::class)) {
			return \null;
		}

		return $this->value == $right->value;

	}

	public function hasRelationTo(string $operator, Value $right): ?bool {

		if (!Func::is_any_of_types($right, NumberValue::class)) {
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
