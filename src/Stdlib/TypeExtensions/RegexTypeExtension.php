<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Values\RegexValue;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Extensions\TypeExtension;
use \Smuuf\Primi\Values\TypeValue;

class RegexTypeExtension extends TypeExtension {

	/**
	 * @primi.function(no-stack)
	 */
	public static function __new__(
		TypeValue $_,
		?AbstractValue $value = \null
	): RegexValue {

		if (!$value instanceof StringValue && !$value instanceof RegexValue) {
			throw new RuntimeError("Invalid argument passed to regex()");
		}

		return Interned::regex($value->getStringValue());

	}

	/**
	 * Regular expression match. Returns the first matching string. Otherwise
	 * returns `false`.
	 *
	 * ```js
	 * rx"[xyz]+".match("abbcxxyzzdeef") == "xxyzz"
	 * ```
	 *
	 * @primi.function
	 */
	public static function match(
		RegexValue $regex,
		StringValue $haystack
	): AbstractValue {

		if (!\preg_match($regex->value, $haystack->value, $matches)) {
			return Interned::bool(\false);
		}

		return Interned::string($matches[0]);

	}

}
