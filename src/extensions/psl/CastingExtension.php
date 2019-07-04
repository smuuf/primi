<?php

namespace Smuuf\Primi\Psl;

use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Structures\StringValue;
use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\Structures\ArrayValue;
use \Smuuf\Primi\Structures\RegexValue;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Structures\Value;

class CastingExtension extends Extension {

	public static function type(Value $value): StringValue {
		return new StringValue($value::TYPE);
	}

	public static function to_string(Value $value): StringValue {

		Common::allowTypes(
			$value,
			StringValue::class, NumberValue::class, BoolValue::class
			// "Regex to string" casting is done in specific method below.
		);

		return new StringValue((string) $value->value);

	}

	public static function to_regex(Value $value): RegexValue {

		// Allow regexes to be casted to regex.
		if ($value instanceof RegexValue) {
			return $value;
		}

		Common::allowTypes(
			$value,
			StringValue::class, NumberValue::class
		);

		return new RegexValue((string) $value->value);

	}

	public static function to_bool(Value $value): BoolValue {

		Common::allowTypes(
			$value,
			StringValue::class, NumberValue::class, ArrayValue::class,
			BoolValue::class
		);

		return new BoolValue(Common::isTruthy($value));

	}

	public static function to_number(Value $value): NumberValue {

		Common::allowTypes(
			$value,
			StringValue::class, NumberValue::class, BoolValue::class
		);

		return new NumberValue((string) $value->value);

	}

	public static function to_array(ArrayValue $value): ArrayValue {

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
