<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Helpers\Func;

class NumberValue extends Value {

	/** @const int Floating point precision for bcmath operations. */
	const PRECISION = 128;
	const TYPE = "number";

	public $_ = 0;

	public function __construct(string $value) {

		if ($value === '') {
			throw new EngineError("Cannot create number from empty string");
		}

		$this->value = Func::normalize_decimal($value);

	}

	public function isTruthy(): bool {
		return (bool) $this->value;
	}

	public function getLength(): ?int {
		return \strlen($this->value);
	}

	public function getStringRepr(): string {
		return Func::normalize_decimal($this->value);
	}

	public function doAddition(Value $right): ?Value {

		if (!$right instanceof NumberValue) {
			return \null;
		}

		return new self(\bcadd($this->value, $right->value, self::PRECISION));

	}

	public function doSubtraction(Value $right): ?Value {

		if (!$right instanceof NumberValue) {
			return \null;
		}

		return new self(\bcsub($this->value, $right->value, self::PRECISION));

	}

	public function doMultiplication(Value $right): ?Value {

		if (!$right instanceof NumberValue) {
			return \null;
		}

		return new self(\bcmul($this->value, $right->value, self::PRECISION));

	}

	public function doDivision(Value $right): ?Value {

		if (!$right instanceof NumberValue) {
			return \null;
		}

		// Avoid division by zero.
		if (\bccomp($right->value, "0") === 0) {
			throw new RuntimeError("Division by zero");
		}

		return new self(\bcdiv($this->value, $right->value, self::PRECISION));

	}

	public function doPower(Value $right): ?Value {

		if (!$right instanceof NumberValue) {
			return \null;
		}

		// If the exponent is a fractional decimal, bcmath can't handle it.
		if (\bccomp(
				\bcmod($right->value, 1, NumberValue::PRECISION),
				'0',
				self::PRECISION
			) === -1
		) {
			throw new RuntimeError("Exponent must be integer");
		}

		return new self(\bcpow($this->value, $right->value, self::PRECISION));

	}

	public function isEqualTo(Value $right): ?bool {

		if ($right instanceof BoolValue) {
			return $this->value == $right->value;
		}

		if ($right instanceof NumberValue) {
			return \bccomp($this->value, $right->value, self::PRECISION) === 0;
		}

		return null;

	}

	public function hasRelationTo(string $operator, Value $right): ?bool {

		if (!$right instanceof NumberValue) {
			return \null;
		}

		$l = $this->value;
		$r = $right->value;

		switch ($operator) {
			case ">":
				return \bccomp($l, $r, self::PRECISION) === 1;
			case "<":
				return \bccomp($l, $r, self::PRECISION) === -1;
			case ">=":
				return \bccomp($l, $r, self::PRECISION) === 1 || \bccomp($l, $r, self::PRECISION) === 0;
			case "<=":
				return \bccomp($l, $r, self::PRECISION) === -1 || \bccomp($l, $r, self::PRECISION) === 0;
		}

		return null;

	}

}
