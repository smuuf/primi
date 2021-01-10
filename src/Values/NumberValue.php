<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Stats;

/**
 * Class for representing numbers in Primi.
 *
 * NOTE: You should _never_ modify the internal $value property directly,
 * as it may later lead to unpredictable results.
 */
class NumberValue extends AbstractValue {

	/** @const int Floating point precision for bcmath operations. */
	const PRECISION = 128;
	const TYPE = "number";

	/** @var array<string, self> Dict for storing interned numbers. */
	private static $interned = [];

	/**
	 * @param string $number Number as string.
	 */
	public static function build($number = null) {

		if ($number === null) {
			throw new EngineError("Missing argument for NumberValue::build()");
		}

		// Numbers up to 5 characters (after normalizing) will be interned.
		if (strlen($number) <= 5) {
			return self::$interned[$number]
				?? (self::$interned[$number] = new self($number));
		}

		return new self($number);

	}

	public function __construct(string $number) {

		if ($number === '') {
			throw new EngineError("Cannot create number from empty string");
		}

		$this->value = Func::normalize_decimal($number);
		Stats::add('values_number');

	}

	public function isTruthy(): bool {

		// Intentionally loose comparison. Better than casting to bool, because:
		// '00.000' == 0 // true (we want that), but
		// (bool) '00.000' // true (and we want false)

		return $this->value != 0;

	}

	public function getLength(): ?int {
		return \strlen($this->value);
	}

	public function getStringRepr(): string {
		return $this->value;
	}

	public function hash(): string {
		return \md5($this->value);
	}

	public function doAddition(AbstractValue $right): ?AbstractValue {

		if (!$right instanceof NumberValue) {
			return \null;
		}

		return new self(bcadd($this->value, $right->value, self::PRECISION));

	}

	public function doSubtraction(AbstractValue $right): ?AbstractValue {

		if (!$right instanceof NumberValue) {
			return \null;
		}

		return new self(bcsub($this->value, $right->value, self::PRECISION));

	}

	public function doMultiplication(AbstractValue $right): ?AbstractValue {

		if (!$right instanceof NumberValue) {
			return \null;
		}

		return new self(bcmul($this->value, $right->value, self::PRECISION));

	}

	public function doDivision(AbstractValue $right): ?AbstractValue {

		if (!$right instanceof NumberValue) {
			return \null;
		}

		// Avoid division by zero.
		if (\bccomp($right->value, "0") === 0) {
			throw new RuntimeError("Division by zero");
		}

		return new self(bcdiv($this->value, $right->value, self::PRECISION));

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
			) === -1
		) {
			throw new RuntimeError("Exponent must be integer");
		}

		return new self(bcpow($this->value, $right->value, self::PRECISION));

	}

	public function isEqualTo(AbstractValue $right): ?bool {

		if ($right instanceof BoolValue) {
			// Comparison with numbers: The only truths:
			// a) 1 == true
			// b) 0 == false
			// Anything else is false.
			// Number is normalized upon construction, so for example '01.00' is
			// stored as '1', or '0.00' is '0', so the mechanism below works.
			return $this->value === ($right->value ? '1' : '0');
		}

		if ($right instanceof NumberValue) {
			return \bccomp($this->value, $right->value, self::PRECISION) === 0;
		}

		return null;

	}

	public function hasRelationTo(string $operator, AbstractValue $right): ?bool {

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
