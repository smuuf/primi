<?php

declare(strict_types=1);

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\ISupportsComparison;

class RegexValue extends Value implements ISupportsComparison {

	const TYPE = "regex";

	/**
	 * Prepared string for 'truthiness' evaluation.
	 * Regex value is truthy if the actual 'internal' regex, without delimiters
	 * and unicode modifier, is empty.
	 */
	const EMPTY_REGEX = "\x07\x07u";

	public function __construct(string $regex) {

		// We'll be using ASCII \x07 (bell) character as delimiters, so
		// we won't need to deal with any escaping of input.
		$this->value = "\x07$regex\x07u";

	}

	public function isTruthy(): bool {
		return $this->value !== self::EMPTY_REGEX;
	}

	public function getStringRepr(): string {

		// Cut off the first delim and the last delim + "u" modifier.
		$string = $this->value;
		$string = \mb_substr($string, 1, \mb_strlen($string) - 3);

		return "r\"{$string}\"";

	}

	public function doComparison(string $operator, Value $rightOperand): BoolValue {

		Common::allowTypes($rightOperand, StringValue::class, NumberValue::class, RegexValue::class);

		if ($operator === "==") {
			if ($rightOperand instanceof RegexValue) {
				return new BoolValue($this->value === $rightOperand->value);
			}
			return new BoolValue((bool) \preg_match($this->value, $rightOperand->value));
		}

		if ($operator === "!=") {
			if ($rightOperand instanceof RegexValue) {
				return new BoolValue($this->value !== $rightOperand->value);
			}
			return new BoolValue((bool) !\preg_match($this->value, $rightOperand->value));
		}

		throw new \TypeError;

	}

}
