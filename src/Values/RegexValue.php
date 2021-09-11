<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Helpers\Func;

/**
 * NOTE: You should not instantiate this PHP class directly - use the helper
 * `Interned::regex()` factory to get these.
 */
class RegexValue extends AbstractNativeValue {

	protected const TYPE = "regex";

	/**
	 * Prepared string for 'truthiness' evaluation.
	 * Regex value is truthy if the actual pattern, without delimiters and
	 * unicode modifier, is empty.
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

		return "rx\"{$string}\"";

	}

	public function isEqualTo(AbstractValue $right): ?bool {

		if (!Func::is_any_of_types($right, StringValue::class, RegexValue::class)) {
			return \null;
		}

		if ($right instanceof RegexValue) {
			return $this->value === $right->value;
		}

		return (bool) \preg_match($this->value, $right->value);

	}

}
