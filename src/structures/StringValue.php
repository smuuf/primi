<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\ISupportsLength;
use \Smuuf\Primi\ISupportsAddition;
use \Smuuf\Primi\ISupportsIteration;
use \Smuuf\Primi\ISupportsKeyAccess;
use \Smuuf\Primi\ISupportsSubtraction;
use \Smuuf\Primi\ISupportsMultiplication;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Ex\IndexError;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\StringEscaping;

class StringValue extends Value implements
	ISupportsAddition,
	ISupportsSubtraction,
	ISupportsMultiplication,
	ISupportsIteration,
	ISupportsKeyAccess,
	ISupportsLength
{

	const TYPE = "string";

	public function __construct(string $value) {
		$this->value = $value;
	}

	public function getLength(): int {
		return \mb_strlen($this->value);
	}

	public function isTruthy(): bool {
		return $this->value !== '';
	}

	public function getStringValue(): string {
		return $this->value;
	}

	public function getStringRepr(): string {

		// We are about to put double-quotes around the return value,
		// so let's "escape" double-quotes present in the string value.
		$escaped = StringEscaping::escapeString($this->value, '"');
		return "\"$escaped\"";

	}

	public function doAddition(Value $right): ?Value {

		if (!$right instanceof StringValue) {
			return \null;
		}

		return new self($this->value . $right->value);

	}

	public function doSubtraction(Value $right): ?Value {

		if (!Func::is_any_of_types($right, StringValue::class, RegexValue::class)) {
			return \null;
		}

		if ($right instanceof RegexValue) {
			$match = \preg_replace($right->value, '', $this->value);
			return new self($match);
		}

		$new = \str_replace($right->value, '', $this->value);
		return new self($new);

	}

	public function doMultiplication(Value $right): ?Value {

		if (!$right instanceof NumberValue) {
			return \null;
		}

		$multiplier = $right->value;
		if (Func::is_round_int($multiplier) && $multiplier >= 0) {
			return new self(\str_repeat($this->value, (int) $multiplier));
		}

		throw new RuntimeError("String multiplier must be a positive integer.");

	}

	public function isEqualTo(Value $right): ?bool {

		if (!Func::is_any_of_types($right, StringValue::class, RegexValue::class)) {
			return \null;
		}

		if ($right instanceof RegexValue) {
			return (bool) \preg_match($right->value, $this->value);
		} else {
			return $this->value === $right->value;
		}

	}

	public function hasRelationTo(string $operator, Value $right): ?bool {

		if (!Func::is_any_of_types($right, StringValue::class)) {
			return \null;
		}

		$l = $this->value;
		$r = $right->value;

		switch ($operator) {
			case ">":
				return $l > $r;
			case "<":
				return $l < $r;
			case ">=":
				return $l >= $r;
			case "<=":
				return $l <= $r;
		}

		return null;

	}

	public function arrayGet(string $index): Value {

		if (!Func::is_round_int($index)) {
			throw new RuntimeError("String index must be integer");
		}

		$index = (int) $index;

		if (!isset($this->value[$index])) {
			throw new IndexError((string) $index);
		}

		return new self($this->value[$index]);

	}

	public function arraySet(?string $index, Value $value) {
		throw new RuntimeError("String does not support assignment.");
	}

	public function getInsertionProxy(?string $index): InsertionProxy {
		throw new RuntimeError("String does not support assignment.");
	}

	public function getIterator(): \Iterator {
		return self::utfSplit($this->value);
	}

	// Helpers.

	/**
	 * Return a generator yielding each of this string's characters as
	 * new one-character StringValue objects.
	 */
	private static function utfSplit(string $string): \Generator {

		$strlen = \mb_strlen($string);
		for ($i = 0; $i < $strlen; $i++) {
			yield new self(\mb_substr($string, $i, 1));
		}

	}

}
