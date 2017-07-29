<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\UnsupportedOperationException;
use \Smuuf\Primi\ISupportsComparison;

class RegexValue extends Value implements
	ISupportsComparison
{

	const TYPE = "regex";

	public function __construct(string $regex) {
		$this->value = $regex . "u";
	}

	public function doComparison(string $operator, Value $rightOperand) {

		if ($operator === "==") {
			return new BoolValue(\preg_match($this->value, $rightOperand->value));
		}

		throw new UnsupportedOperationException;

	}

}
