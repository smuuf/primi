<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\ISupportsComparison;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Structures\Value;

class NullValue extends Value implements ISupportsComparison {

	const TYPE = "null";

	protected $value = null;

	public function getStringValue(): string {
		return "null";
	}

	public function doComparison(string $op, Value $rightOperand): BoolValue {

		Common::allowTypes($rightOperand, self::class, BoolValue::class);

		switch ($op) {
			case "==":
				return new BoolValue($this->value == $rightOperand->value);
			case "!=":
				return new BoolValue($this->value != $rightOperand->value);
			default:
				throw new \TypeError;
		}

	}

}
