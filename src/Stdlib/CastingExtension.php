<?php

namespace Smuuf\Primi\Stdlib;

use \Smuuf\Primi\Extensions\Extension;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Values\DictValue;
use \Smuuf\Primi\Values\ListValue;
use \Smuuf\Primi\Values\RegexValue;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Values\AbstractValue;

class CastingExtension extends Extension {

	/**
	 * Return a `string` representation of value.
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
	public static function to_string(AbstractValue $value): StringValue {
		return StringValue::build($value->getStringValue());
	}

	/**
	 * Convert `string` to a `regex` value. If the optional `escape` argument
	 * is `true`, the any characters with special regex meaning will be
	 * escaped so that they are meant literally.
	 *
	 * ```js
	 * "hello".to_regex() == rx"hello"
	 * to_regex("Why so serious...?", true) == rx"Why so serious\.\.\.\?"
	 * ```
	 */
	public static function to_regex(
		AbstractValue $value,
		?BoolValue $escape = null
	): RegexValue {

		// Allow regexes to be casted to regex.
		if ($value instanceof RegexValue) {
			return $value;
		}

		Func::allow_argument_types(
			0,
			$value,
			StringValue::class, RegexValue::class
		);

		$regex = $value->value;
		if ($escape && $escape->isTruthy()) {
			$regex = \preg_quote($regex);
		}

		return new RegexValue($regex);

	}

	/**
	 * Returns a new `bool` value based on argument's truthness.
	 *
	 * ```js
	 * to_bool(true) == true
	 * to_bool(false) == false
	 * to_bool(-1) == true
	 * to_bool(0) == false
	 * to_bool([]) == false
	 * to_bool(['']) == true
	 * to_bool('') == false
	 * to_bool(' ') == true
	 * to_bool('0') == true
	 * to_bool('1') == true
	 * ```
	 */
	public static function to_bool(AbstractValue $value): BoolValue {
		return BoolValue::build($value->isTruthy());
	}

	/**
	 * Cast a `number|string|bool` value to `number`.
	 *
	 * ```js
	 * to_number(1) == 1
	 * to_number('123') == 123
	 * to_number('+123') == 123
	 * to_number('-123') == -123
	 * to_number(' +123.001   ') == 123.001
	 * to_number(' -123.00   ') == -123.0
	 *
	 * to_number(true) == 1
	 * to_number(false) == 0
	 * to_number(fal) == 0
	 * ```
	 */
	public static function to_number(AbstractValue $value): NumberValue {

		if ($value instanceof NumberValue) {
			// numberValue is immutable, so we don't even need to make a clone.
			return $value;
		}

		if ($value instanceof BoolValue) {
			return NumberValue::build($value->value ? '1' : '0');
		}

		Func::allow_argument_types(
			0,
			$value,
			StringValue::class, NumberValue::class, BoolValue::class
		);

		$str = \trim($value->value);
		if (!Func::is_decimal($str)) {
			throw new RuntimeError("Invalid number literal '$str'");
		}

		return NumberValue::build((string) $str);

	}

	/**
	 * Returns a new `list` containing items of some iterable value.
	 *
	 * ```js
	 * a_list = 'první máj'.to_list()
	 * a_list == ["p", "r", "v", "n", "í", " ", "m", "á", "j"]
	 *
	 * b_list = {'a': 1, 'b': 2, 'c': []}.to_list()
	 * b_list = [1, 2, []]
	 *
	 * c_list = {'a': 1, 'b': 2, 'c': []}.keys().to_list()
	 * c_list = ['a', 'b', 'c']
	 * ```
	 */
	public static function to_list(AbstractValue $value): ListValue {

		$iter = $value->getIterator();
		if ($iter === null) {
			throw new RuntimeError(\sprintf(
				"Type '%s' cannot be casted to list", $value::TYPE
			));
		}

		// Some iterators (eg. dict) can return objects as keys and that
		// would make iterator_to_array() crash with "Illegal offset type",
		// so we have to make sure only values are taken from the iterator.
		$values = (function($iter) {
			foreach ($iter as $v) {	yield $v; }
		})($iter);

		return new ListValue(\iterator_to_array($values));

	}

	/**
	 * Returns a new dict containing items of some iterable value.
	 *
	 * ```js
	 * a_list = 'máj'.to_dict()
	 * a_list == {0: 'm', 1: 'á', 2: 'j'}
	 *
	 * b_list = {'a': 1, 'b': 2, 'c': []}.to_list()
	 * b_list = [1, 2, []]
	 *
	 * c_list = {'a': 1, 'b': 2, 'c': []}.keys().to_list()
	 * c_list = ['a', 'b', 'c']
	 * ```
	 */
	public static function to_dict(AbstractValue $value): DictValue {

		if ($value instanceof DictValue) {
			return clone $value;
		}

		Func::allow_argument_types(
			0,
			$value,
			StringValue::class, ListValue::class
		);

		// Allow arrays to be casted to array (no other conversions allowed).
		// And do NOT break references inside the array (arbitrary decision).
		return new DictValue(Func::iterator_as_tuples($value->getIterator()));

	}

	// Specific cases of casting.

	/**
	 * Regex delimiters must be trimmed off when converting to string.
	 *
	 * @docgenSkip
	 */
	public static function regex_to_string(RegexValue $value): StringValue {

		// Cut off the first delim and the last delim + "u" modifier.
		$string = $value->value;
		$string = \mb_substr($string, 1, \mb_strlen($string) - 3);

		return StringValue::build($string);
	}

}
