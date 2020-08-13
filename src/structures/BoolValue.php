<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Structures\NumberValue;

use function \Smuuf\Primi\Helpers\is_any_of_types as primifn_is_any_of_types;

class BoolValue extends Value {

	const TYPE = "bool";

	public function __construct(bool $value) {
		$this->value = $value;
	}

	public function getStringRepr(): string {
		return $this->value ? 'true' : 'false';
	}

	public function isTruthy(): bool {
		return $this->value;
	}

	public function isEqualTo(Value $right): ?bool {

		if (!primifn_is_any_of_types($right, BoolValue::class, NumberValue::class)) {
			return \null;
		}

		return $this->value === $right->isTruthy();

	}

}
