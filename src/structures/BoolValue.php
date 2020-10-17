<?php

declare(strict_types=1);

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Structures\NumberValue;

class BoolValue extends Value {

	const TYPE = "bool";

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

	public function isEqualTo(Value $right): ?bool {

		if (!Func::is_any_of_types($right, BoolValue::class, NumberValue::class)) {
			return \null;
		}

		return $this->value === $right->isTruthy();

	}

}
