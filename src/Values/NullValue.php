<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Helpers\Stats;
use \Smuuf\Primi\Values\AbstractValue;

class NullValue extends AbstractValue {

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

	public function isEqualTo(AbstractValue $right): ?bool {

		if (!$right instanceof self) {
			return \null;
		}

		return $this->value === $right->value;

	}

}
