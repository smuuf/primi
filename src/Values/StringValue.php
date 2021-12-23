<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Ex\IndexError;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Stdlib\StaticTypes;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\StringEscaping;

/**
 * NOTE: You should not instantiate this PHP class directly - use the helper
 * `Interned::string()` factory to get these.
 */
class StringValue extends AbstractNativeValue {

	protected const TYPE = "string";

	public function __construct(string $str) {
		$this->value = $str;
	}

	public function getType(): TypeValue {
		return StaticTypes::getStringType();
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

		// PHP interns all strings by default, so use the string itself as
		// the hash, as doing anything more would be more expensive.
		return $this->value;

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

		throw new RuntimeError("String multiplier must be a positive integer");

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

		switch (\true) {
			case $operator === ">":
				return $l > $r;
			case $operator ===  "<":
				return $l < $r;
			case $operator ===  ">=":
				return $l >= $r;
			case $operator === "<=":
				return $l <= $r;
		}

		return \null;

	}

	public function itemGet(AbstractValue $index): AbstractValue {

		if (!Func::is_round_int($index->value)) {
			throw new RuntimeError("String index must be integer");
		}

		// Even though in other "container" types (eg. list, tuple) we need
		// to manually handle negative indices via helper
		// function Indices::resolveIndexOrError(), PHP supports negative
		// indixes for strings  out-of-the-box since PHP 7.1, so we don't need
		// to do any magic here.
		// See https://wiki.php.net/rfc/negative-string-offsets

		$index = (int) $index->value;
		if (!isset($this->value[$index])) {
			throw new IndexError($index);
		}

		return new self($this->value[$index]);

	}

	/**
	 * @return \Iterator<int, AbstractValue>
	 */
	public function getIterator(): \Iterator {
		return self::utfSplit($this->value);
	}

	public function doesContain(AbstractValue $right): ?bool {

		if (!Func::is_any_of_types($right, StringValue::class, RegexValue::class)) {
			throw new TypeError("'in' for string requires 'string|regex' as left operand");
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
	 *
	 * @return \Generator<int, self, null, void>
	 */
	private static function utfSplit(string $string): \Generator {

		$strlen = \mb_strlen($string);
		for ($i = 0; $i < $strlen; $i++) {
			yield new self(\mb_substr($string, $i, 1));
		}

	}

}
