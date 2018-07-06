<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\ISupportsComparison;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Structures\NullValue;

class BoolValue extends Value implements ISupportsComparison {

	const TYPE = "bool";

	public function __construct(bool $value) {
		$this->value = $value;
	}

	public function getStringValue(): string {
		return $this->value ? 'true' : 'false';
	}

	public function doComparison(string $op, Value $rightOperand): BoolValue {

		Common::allowTypes($rightOperand, self::class, NullValue::class);

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
