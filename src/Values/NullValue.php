<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Values\AbstractValue;

/**
 * NOTE: You should not instantiate this PHP class directly - use the helper
 * `Interned::null()` factory to get these.
 */
class NullValue extends AbstractNativeValue {

	protected const TYPE = "Null";

	public function getStringRepr(): string {
		return "null";
	}

	public function hash(): string {
		return 'n';
	}

	public function isTruthy(): bool {
		return \false;
	}

	public function isEqualTo(AbstractValue $right): ?bool {

		if (!$right instanceof self) {
			return \null;
		}

		return $this->value === $right->value;

	}

}
