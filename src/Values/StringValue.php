<?php

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Helpers\Stats;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Ex\IndexError;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\StringEscaping;

class StringValue extends AbstractValue {

	const TYPE = "string";

	public function __construct(string $value) {

		Stats::add('value_count_string');
		$this->value = $value;

	}

	public function getLength(): ?int {
		return \mb_strlen($this->value);
	}

	public function isTruthy(): bool {
		return $this->value !== '';
	}

	public function getStringValue(): string {
		return $this->value;
	}

	public function hash(): string {
		return \md5($this->value);
	}

	public function getStringRepr(): string {

		// We are about to put double-quotes around the return value,
		// so let's "escape" double-quotes present in the string value.
		$escaped = StringEscaping::escapeString($this->value, '"');
		return "\"$escaped\"";

	}

	public function doAddition(AbstractValue $right): ?AbstractValue {

		if (!$right instanceof StringValue) {
			return \null;
		}

		return new self($this->value . $right->value);

	}

	public function doSubtraction(AbstractValue $right): ?AbstractValue {

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

	public function doMultiplication(AbstractValue $right): ?AbstractValue {

		if (!$right instanceof NumberValue) {
			return \null;
		}

		$multiplier = $right->value;
		if (Func::is_round_int($multiplier) && $multiplier >= 0) {
			return new self(\str_repeat($this->value, (int) $multiplier));
		}

		throw new RuntimeError("String multiplier must be a positive integer.");

	}

	public function isEqualTo(AbstractValue $right): ?bool {

		if (!Func::is_any_of_types($right, StringValue::class, RegexValue::class)) {
			return \null;
		}

		if ($right instanceof RegexValue) {
			return (bool) \preg_match($right->value, $this->value);
		} else {
			return $this->value === $right->value;
		}

	}

	public function hasRelationTo(string $operator, AbstractValue $right): ?bool {

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

		return \null;

	}

	public function itemGet(AbstractValue $index): AbstractValue {

		if (!Func::is_round_int($index->value)) {
			throw new RuntimeError("String index must be integer");
		}

		$index = (int) $index->value;

		if (!isset($this->value[$index])) {
			throw new IndexError((string) $index);
		}

		return new self($this->value[$index]);

	}

	public function getIterator(): \Iterator {
		return self::utfSplit($this->value);
	}

	public function doesContain(AbstractValue $right): ?bool {

		if (!Func::is_any_of_types($right, StringValue::class, RegexValue::class)) {
			throw new TypeError("'in' for string requires 'string|regex' as left operand.");
		}

		if ($right instanceof RegexValue) {
			return (bool) \preg_match($right->value, $this->value);
		}

		return \mb_strpos($this->value, $right->value) !== \false;

	}

	// Helpers.

	/**
	 * Return a generator yielding each of this string's characters as
	 * new one-character StringValue objects.
	 */
	private static function utfSplit(string $string): \Generator {

		$strlen = \mb_strlen($string);
		for ($i = 0; $i < $strlen; $i++) {

			yield new NumberValue((string) $i)
				=> new self(\mb_substr($string, $i, 1));

		}

	}

}
