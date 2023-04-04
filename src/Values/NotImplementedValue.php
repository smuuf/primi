<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use Smuuf\Primi\Stdlib\StaticTypes;
use Smuuf\Primi\Values\AbstractValue;

/**
 * Special constant object representing a "not implemented" information usable
 * in userland.
 */
class NotImplementedValue extends AbstractBuiltinValue {

	public const TYPE = "NotImplementedType";

	public function getType(): TypeValue {
		return StaticTypes::getNotImplementedType();
	}

	public function getStringRepr(): string {
		return "NotImplemented";
	}

	public function hash(): string {
		return 'const:notimplemented';
	}

	public function isTruthy(): bool {
		return \true;
	}

	public function isEqualTo(AbstractValue $right): ?bool {

		// NotImplemented doesn't know or care about other types - the
		// relationship is undefined from its point of view.
		if (!$right instanceof self) {
			return \null;
		}

		// NotImplemented is always equal to NotImplemented.
		return \true;

	}

}
