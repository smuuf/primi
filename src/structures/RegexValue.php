<?php

declare(strict_types=1);

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\ISupportsComparison;

class RegexValue extends Value implements ISupportsComparison {

	const TYPE = "regex";

	public function __construct(string $regex) {

		// We'll be using ASCII \x07 (bell) character as delimiters, so
		// we won't need to deal with any escaping of input.
		$this->value = "\x07$regex\x07u";

	}

	public function getStringValue(): string {

		// Cut off the first delim and the last delim + "u" modifier.
		$string = $this->value;
		$string = \mb_substr($string, 1, \mb_strlen($string) - 3);

		return "r\"{$string}\"";

	}

	public function doComparison(string $operator, Value $rightOperand): BoolValue {

		Common::allowTypes($rightOperand, StringValue::class, NumberValue::class);

		if ($operator === "==") {
			return new BoolValue((bool) \preg_match($this->value, $rightOperand->value));
		}

		if ($operator === "!=") {
			return new BoolValue((bool) !\preg_match($this->value, $rightOperand->value));
		}

		throw new \TypeError;

	}

}
