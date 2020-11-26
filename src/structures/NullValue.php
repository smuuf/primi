<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Stats;
use \Smuuf\Primi\Structures\Value;

class NullValue extends Value {

	const TYPE = "null";

	public function __construct() {
		Stats::add('value_count_null');
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

	public function isEqualTo(Value $right): ?bool {

		if (!$right instanceof self) {
			return \null;
		}

		return $this->value === $right->value;

	}

}
