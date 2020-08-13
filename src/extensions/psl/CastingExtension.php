<?php

namespace Smuuf\Primi\Psl;

use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Structures\StringValue;
use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\Structures\DictValue;
use \Smuuf\Primi\Structures\ListValue;
use \Smuuf\Primi\Structures\RegexValue;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Structures\Value;

use function \Smuuf\Primi\Helpers\allow_argument_types as primifn_allow_argument_types;

class CastingExtension extends Extension {

	/**
	 * Return type of value as string.
	 *
	 * ```js
	 * type(true) == 'bool'
	 * type("hello") == 'string'
	 * type(type) == 'function'
	 * ```
	 */
	public static function type(Value $value): StringValue {
		return new StringValue($value::TYPE);
	}

	/**
	 * Return a string representation of value.
	 *
	 * ```js
	 * to_string(true) == 'true'
	 * to_string([]) == '[]'
	 * to_string(3.14) == '3.14'
	 * {'a': 1, 'b': 'c'}.to_string() == '{"a": 1, "b": "c"}'
	 * "hello there!".to_string() == "hello there!"
	 * to_string(to_string) == "<function: native>"
	 * ```
	 */
	public static function to_string(Value $value): StringValue {
		return new StringValue($value->getStringValue());
	}

	public static function to_regex(Value $value): RegexValue {

		// Allow regexes to be casted to regex.
		if ($value instanceof RegexValue) {
			return $value;
		}

		primifn_allow_argument_types(
			0,
			$value,
			StringValue::class, NumberValue::class
		);

		return new RegexValue((string) $value->value);

	}

	public static function to_bool(Value $value): BoolValue {
		return new BoolValue($value->isTruthy());
	}

	public static function to_number(Value $value): NumberValue {

		primifn_allow_argument_types(
			0,
			$value,
			StringValue::class, NumberValue::class, BoolValue::class
		);

		return new NumberValue((string) $value->value);

	}

	public static function to_list(Value $value): ListValue {

		primifn_allow_argument_types(0, $value, ListValue::class, StringValue::class);

		if ($value instanceof StringValue) {
			return new ListValue(iterator_to_array($value->getIterator()));
		}

		// Allow arrays to be casted to array (no other conversions allowed).
		// And do NOT break references inside the array (arbitrary decision).
		return $value;

	}

	public static function to_dict(DictValue $value): DictValue {

		// Allow arrays to be casted to array (no other conversions allowed).
		// And do NOT break references inside the array (arbitrary decision).
		return $value;

	}

	// Specific cases of casting.

	public static function regex_to_string(RegexValue $value): StringValue {

		// Cut off the first delim and the last delim + "u" modifier.
		$string = $value->value;
		$string = \mb_substr($string, 1, \mb_strlen($string) - 3);

		return new StringValue($string);
	}

}
