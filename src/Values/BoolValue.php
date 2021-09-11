<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Values\NumberValue;

/**
 * NOTE: You should not instantiate this PHP class directly - use the helper
 * `Interned::bool()` factory to get these.
 */
class BoolValue extends AbstractNativeValue {

	protected const TYPE = "bool";

	public function __construct(bool $value) {
		$this->value = $value;
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
