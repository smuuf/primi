<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Helpers\Stats;

class BoolValue extends AbstractValue {

	const TYPE = "bool";

	/** @var array<int, self> Dict for storing interned bool values. */
	private static $interned = null;

	public static function build($truth = null) {

		if ($truth === null) {
			throw new EngineError("Missing argument for BoolValue::build()");
		}

		return self::$interned[$truth]
			?? (self::$interned[$truth] = new self($truth));

	}

	public function __construct(bool $value) {
		$this->value = $value;
		Stats::add('values_bool');
	}

	public function getStringRepr(): string {
		return $this->value ? 'true' : 'false';
	}

	public function hash(): string {
		return $this->value ? '1' : '0';
	}

	public function isTruthy(): bool {
		return $this->value;
	}

	public function isEqualTo(AbstractValue $right): ?bool {

		if ($right instanceof NumberValue) {
			// Comparison with numbers - the only rules:
			// a) 1 == true
			// b) 0 == false
			// c) Anything else is false.
			// Number is normalized upon construction, so for example '01.00' is
			// stored as '1', or '0.00' is '0', so the mechanism below works.
			return $right->value === ($this->value ? '1' : '0');
		}

		if (!$right instanceof BoolValue) {
			return \null;
		}

		return $this->value === $right->isTruthy();

	}

}
