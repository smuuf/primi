<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use Smuuf\Primi\Stdlib\StaticTypes;
use Smuuf\Primi\Values\BoolValue;
use Smuuf\Primi\Values\TypeValue;
use Smuuf\Primi\Values\RegexValue;
use Smuuf\Primi\Values\StringValue;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Helpers\Interned;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\Extensions\PrimiFunc;
use Smuuf\Primi\Extensions\TypeExtension;

class RegexTypeExtension extends TypeExtension {

	#[PrimiFunc]
	public static function __new__(
		TypeValue $type,
		?AbstractValue $value = \null
	): RegexValue {

		if ($type !== StaticTypes::getRegexType()) {
			Exceptions::piggyback(
				StaticExceptionTypes::getTypeErrorType(),
				"Passed invalid type object",
			);
		}

		$value ??= Interned::string('');

		if (!$value instanceof StringValue && !$value instanceof RegexValue) {
			Exceptions::piggyback(
				StaticExceptionTypes::getTypeErrorType(),
				"Invalid argument passed to regex()",
			);
		}

		return Interned::regex($value->getStringValue());

	}

	/**
	 * Regular expression find. Returns the first occurence of matching string.
	 * Otherwise returns `false`.
	 *
	 * ```js
	 * rx"[xyz]+".find("abbcxxyzzdeef") == "xxyzz"
	 * ```
	 */
	#[PrimiFunc]
	public static function find(
		RegexValue $regex,
		StringValue $haystack
	): StringValue|BoolValue {

		if (!\preg_match($regex->value, $haystack->value, $matches)) {
			return Interned::bool(\false);
		}

		return Interned::string($matches[0]);

	}

}
