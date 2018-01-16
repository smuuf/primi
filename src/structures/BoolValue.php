<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\ISupportsComparison;

class BoolValue extends Value implements ISupportsComparison {

	const TYPE = "bool";

	public function __construct(bool $value) {
		$this->value = $value;
	}

	public function getStringValue(): string {
		return $this->value ? 'true' : 'false';
	}

	public function doComparison(string $op, Value $rightOperand): BoolValue {

		self::allowTypes($rightOperand, self::class);

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
