<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\ISupportsComparison;

class RegexValue extends Value implements
	ISupportsComparison
{

	const TYPE = "regex";

	public function __construct(string $regex) {
		$this->value = $regex . "u";
	}

	public function getStringValue(): string {
		return $this->value;
	}

	public function doComparison(string $operator, Value $rightOperand): BoolValue {

		self::allowTypes($rightOperand, StringValue::class, NumberValue::class);

		if ($operator === "==") {
			return new BoolValue(\preg_match($this->value, $rightOperand->value));
		}

		if ($operator === "!=") {
			return new BoolValue(!\preg_match($this->value, $rightOperand->value));
		}

		throw new \TypeError;

	}

}
