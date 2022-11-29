<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Stdlib\BuiltinTypes;
use \Smuuf\Primi\Values\AbstractValue;

/**
 * NOTE: You should not instantiate this PHP class directly - use the helper
 * `Interned::null()` factory to get these.
 */
class NullValue extends AbstractBuiltinValue {

	public const TYPE = "null";

	public function getType(): TypeValue {
		return BuiltinTypes::getNullType();
	}

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

		// Null doesn't know or care about other types - the relationship
		// is undefined from its point of view.
		if (!$right instanceof self) {
			return \null;
		}

		// Null is always equal to null.
		return \true;

	}

}
