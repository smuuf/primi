<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\ISupportsComparison;
use \Smuuf\Primi\Structures\NullValue;
use \Smuuf\Primi\Structures\NumberValue;

class BoolValue extends Value implements ISupportsComparison {

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

	public function doComparison(string $op, Value $right): BoolValue {

		Common::allowTypes(
			$right,
			self::class,
			NumberValue::class,
			NullValue::class
		);

		switch ($op) {
			case "==":
				return $this->value === $right->isTruthy();
			case "!=":
				return $this->value !== $right->isTruthy();
			default:
				throw new \TypeError;
		}

	}

}
