<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use Smuuf\Primi\Stdlib\StaticTypes;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Helpers\Exceptions;

/**
 * NOTE: You should not instantiate this PHP class directly - use the helper
 * `Interned::number()` factory to get these.
 *
 * NOTE: You should _never_ modify the internal $value property directly,
 * as it may later lead to unpredictable results.
 */
class NumberValue extends AbstractBuiltinValue {

	/** @var int Floating point precision for bcmath operations. */
	public const PRECISION = 128;

	public const TYPE = "number";

	/**
	 * NOTE: Protected because you should use the Interned factory for building
	 * these.
	 */
	public function __construct(string $number) {

		// Exclude "0" from the regex, so we allow "00" and "0" to become
		// just "0" via normalizing.
		if (\preg_match('#^[1-9]+$#S', $number)) {
			$this->value = $number;
		} else {
			$this->value = Func::normalize_decimal($number);
		}

	}

	public function __debugInfo(): array {
		return [
			'value' => $this->value,
		];
	}

	public function getType(): TypeValue {
		return StaticTypes::getNumberType();
	}

	public function isTruthy(): bool {

		// Intentionally loose comparison. Better than casting to bool, because:
		// '00.000' == 0 // true (we want that), but
		// (bool) '00.000' // true (and we want false)

		return $this->value != 0;

	}

	public function getStringRepr(): string {
		return $this->value;
	}

	public function hash(): string {

		// PHP interns all strings (which is how we internally represent
		// numbers) by default, so use the string itself as the hash, as doing
		// anything more would be more expensive.
		return $this->value;

	}

	public function doAddition(AbstractValue $right): ?AbstractValue {

		if (!$right instanceof NumberValue) {
			return \null;
		}

		return new self(\bcadd($this->value, $right->value, self::PRECISION));

	}

	public function doSubtraction(AbstractValue $right): ?AbstractValue {

		if (!$right instanceof NumberValue) {
			return \null;
		}

		return new self(\bcsub($this->value, $right->value, self::PRECISION));

	}

	public function doMultiplication(AbstractValue $right): ?AbstractValue {

		// Strings can be multiplied.
		if ($right instanceof StringValue) {
			return $right->doMultiplication($this);
		}

		if (!$right instanceof NumberValue) {
			return \null;
		}

		return new self(\bcmul($this->value, $right->value, self::PRECISION));

	}

	public function doDivision(AbstractValue $right): ?AbstractValue {

		if (!$right instanceof NumberValue) {
			return \null;
		}

		// Avoid division by zero.
		if (\bccomp($right->value, "0") === 0) {
			Exceptions::piggyback(
				StaticExceptionTypes::getRuntimeErrorType(),
				"Division by zero",
			);
		}

		return new self(\bcdiv($this->value, $right->value, self::PRECISION));

	}

	public function doPower(AbstractValue $right): ?AbstractValue {

		if (!$right instanceof NumberValue) {
			return \null;
		}

		// If the exponent is a fractional decimal, bcmath can't handle it.
		if (\bccomp(
				\bcmod($right->value, '1', NumberValue::PRECISION),
				'0',
				self::PRECISION
			) !== 0
		) {
			Exceptions::piggyback(
				StaticExceptionTypes::getTypeErrorType(),
				"Exponent must be integer",
			);
		}

		return new self(\bcpow($this->value, $right->value, self::PRECISION));

	}

	public function isEqualTo(AbstractValue $right): ?bool {

		if ($this === $right) {
			return \true;
		}

		if ($right instanceof BoolValue) {

			// Comparison with numbers: The only truths:
			// a) 1 == true
			// b) 0 == false
			// Anything else is false.
			return !\bccomp(
				(string) (int) $right->value, // Create "0" or "1" from bool.
				$this->value,
				self::PRECISION,
			);

		}

		if ($right instanceof NumberValue) {
			return \bccomp($this->value, $right->value, self::PRECISION) === 0;
		}

		return \null;

	}

	public function hasRelationTo(string $operator, AbstractValue $right): ?bool {

		if (!$right instanceof NumberValue) {
			return \null;
		}

		$l = $this->value;
		$r = $right->value;

		switch (\true) {
			case $operator === ">":
				return \bccomp($l, $r, self::PRECISION) === 1;
			case $operator === "<":
				return \bccomp($l, $r, self::PRECISION) === -1;
			case $operator === ">=":
				$tmp = \bccomp($l, $r, self::PRECISION);
				return $tmp === 1 || $tmp === 0;
			case $operator === "<=":
				$tmp = \bccomp($l, $r, self::PRECISION);
				return $tmp === -1 || $tmp === 0;
		}

		return \null;

	}

}
