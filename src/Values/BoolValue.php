<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Helpers\Stats;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Values\NumberValue;

class BoolValue extends AbstractValue {

	const TYPE = "bool";

	public function __construct(bool $value) {
		$this->value = $value;
		Stats::add('value_count_bool');
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

		if (!Func::is_any_of_types($right, BoolValue::class, NumberValue::class)) {
			return \null;
		}

		return $this->value === $right->isTruthy();

	}

}
