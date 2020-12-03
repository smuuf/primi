<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib;

use \Smuuf\Primi\Extensions\Extension;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\ArgumentCountError;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\NullValue;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Values\DictValue;
use \Smuuf\Primi\Values\ListValue;
use \Smuuf\Primi\Values\RegexValue;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\NumberValue;

class StringExtension extends Extension {

	/**
	 * Returns a new `string` from shuffled characters of the original `string`.
	 *
	 * ```js
	 * "hello".shuffle() // "leohl" or something similar.
	 * ```
	 */
	public static function string_shuffle(StringValue $str): StringValue {

		// str_shuffle() doesn't work with unicode, so let's do this ourselves.
		$original = $str->value;
		$length = \mb_strlen($original);
		$indices = \range(0, $length - 1);
		\shuffle($indices);
		$result = "";

		while (($i = \array_pop($indices)) !== \null) {
			$result .= \mb_substr($original, $i, 1);
		}

		return StringValue::build($result);

	}

	/**
	 * Returns a new `string` with placeholders from the original `string`
	 * replaced by additional arguments.
	 *
	 * Placeholders can be either _(but these can't be combined)_:
	 * - Non-positional: `{}`
	 * - Positional: `{0}`, `{1}`, `{2}`, etc.
	 *
	 * ```js
	 * "x{}x, y{}y".format(1, 2) == "x1x, y2y"
	 * "x{1}x, y{0}y".format(111, 222) == "x222x, y111y"
	 * ```
	 */
	public static function string_format(
		StringValue $str,
		AbstractValue ...$items
	): StringValue {

		// Extract PHP values from passed in value objects, because later we
		// will pass the values to sprintf().
		$items = \array_map(function($item) {
			return $item->getStringValue();
		}, $items);

		$passedCount = \count($items);
		$expectedCount = 0;
		$indexedMode = \null;

		// Convert {} syntax to a something sprintf() understands.
		// {} will be converted to "%s", positional {456} will be converted to
		// "%456$s".
		$prepared = \preg_replace_callback("#\{(\d+)?\}#", function($m) use (
			$passedCount,
			&$indexedMode,
			&$expectedCount
		) {

			if (isset($m[1])) {

				// A positional placeholder was used when a non-positional one
				// is already present.
				if ($indexedMode === \false) {
					throw new RuntimeError("Cannot combine positional and non-positional placeholders.");
				}

				$indexedMode = \true;
				$index = $m[1];

				if ($index < 0) {
					throw new RuntimeError("Position ($index) cannot be less than 0.");
				}

				if ($index > $passedCount) {
					throw new RuntimeError("Position ($index) does not match the number of parameters ($passedCount).");
				}

				$plusOne = $index + 1;
				$converted = "%{$plusOne}\$s";

			} else {

				if ($indexedMode === \true) {
					// A non-positional placeholder was used when a positional
					// one is already present.
					throw new RuntimeError(
						\sprintf("Cannot combine positional and non-positional placeholders.")
					);
				}

				$indexedMode = \false;
				$converted = "%s";

			}

			$expectedCount++;
			return $converted;

		}, $str->value);

		// If there are more args expected than passed, throw error.
		if ($expectedCount > $passedCount) {
			throw new RuntimeError(
				\sprintf(
					"Not enough arguments passed (expected %s, got %s).",
					$expectedCount,
					$passedCount
				)
			);
		}

		return StringValue::build(\sprintf($prepared, ...$items));

	}

	/**
	 * Perform search and replace and return the results as new `string`.
	 *
	 * Two separate modes of operation:
	 * 1. The needle `search` is a `string` and haystack `replace` is a string.
	 * 2. The needle `search` is a `dict` defining search-and-replace pairs
	 * _(and `replace` argument is omitted)_.
	 *
	 * ```js
	 * "abcdef".replace("c", "X") == "abXdef"
	 * "abcdef".replace({"c": "X", "e": "Y"}) == "abXdYf"
	 * ```
	 */
	public static function string_replace(
		StringValue $string,
		AbstractValue $search,
		StringValue $replace = \null
	): StringValue {

		// Replacing using array of search-replace pairs.
		if ($search instanceof DictValue) {

			// Extract <from: to> pairs from the dict.
			$from = [];
			$to = [];
			foreach ($search->getIterator() as $key => $value) {

				if (!$key instanceof StringValue) {
					$type = $key::TYPE;
					throw new RuntimeError("Replacement dict key must be a string, '$type' given.");
				}

				if (!$value instanceof StringValue) {
					$type = $value::TYPE;
					throw new RuntimeError("Replacement dict value must be a string, '$type' given.");
				}

				$from[] = $key->value;
				$to[] = $value->value;

			}

			return StringValue::build(\str_replace($from, $to, $string->value));

		}

		if ($replace === \null) {
			throw new ArgumentCountError(2, 3);
		}

		if ($search instanceof StringValue || $search instanceof NumberValue) {
			// Handle both string/number values the same way.
			return StringValue::build(
				\str_replace(
					(string) $search->value, $replace->value, $string->value
				)
			);
		} elseif ($search instanceof RegexValue) {
			return StringValue::build(
				\preg_replace(
					$search->value, $replace->value, $string->value
				)
			);
		} else {

			$type = $search::TYPE;
			throw new RuntimeError("Cannot use '$type' as needle");

		}

	}

	/**
	 * Return reversed string.
	 *
	 * ```js
	 * "hello! tady čaj".reverse() == "jač ydat !olleh"
	 * ```
	 */
	public static function string_reverse(StringValue $string): StringValue {

		// strrev() does not support multibyte.
		// Let's do it ourselves then!

		$result = '';
		$len = \mb_strlen($string->value);

		for ($i = $len; $i-- > 0;) {
			$result .= \mb_substr($string->value, $i, 1);
		}

		return StringValue::build($result);

	}

	/**
	 * Split original `string` by some `delimiter` and return result the as a
	 * `list`. If the `delimiter` is not specified, the `string` is splat by
	 * whitespace characters.
	 *
	 * ```js
	 * "a b c\nd e f".split() == ['a', 'b', 'c', 'd', 'e', 'f']
	 * "a,b,c,d".split(',') == ['a', 'b', 'c', 'd']
	 * ```
	 */
	public static function string_split(
		StringValue $string,
		?AbstractValue $delimiter = \null
	): ListValue {

		// Split by whitespaces by default.
		if ($delimiter === \null) {
			$delimiter = new RegexValue('\s+');
		}

		// Allow only some value types.
		Func::allow_argument_types(1, $delimiter, StringValue::class, RegexValue::class);

		if ($delimiter instanceof RegexValue) {
			$splat = \preg_split($delimiter->value, $string->value);
		}

		if ($delimiter instanceof StringValue) {
			if ($delimiter->value === '') {
				throw new RuntimeError("String delimiter must not be empty.");
			}
			$splat = \explode($delimiter->value, $string->value);
		}

		return new ListValue(\array_map(function($part) {
			return StringValue::build($part);
		}, $splat ?? []));

	}

	/**
	 * Returns `true` if the `string` contains `needle`. Returns `false`
	 * otherwise.
	 *
	 * ```js
	 * "this is a sentence".contains("sen") == true
	 * "this is a sentence".contains("yay") == false
	 * ```
	 */
	public static function string_contains(
		StringValue $haystack,
		AbstractValue $needle
	): BoolValue {
		return BoolValue::build($haystack->doesContain($needle));
	}

	/**
	 * Returns `number` of occurrences of `needle` in a string.
	 *
	 * ```js
	 * "this is a sentence".number_of("s") == 3
	 * "this is a sentence".number_of("x") == 0
	 * ```
	 */
	public static function string_number_of(
		StringValue $haystack,
		AbstractValue $needle
	): NumberValue {

		// Allow only some value types.
		Func::allow_argument_types(1, $needle, StringValue::class, NumberValue::class);

		$count = (string) \mb_substr_count(
			(string) $haystack->value,
			(string) $needle->value
		);

		return NumberValue::build($count, true);

	}

	/**
	 * Returns the position _(index)_ of **first** occurrence of `needle` in
	 * the `string`. If the `needle` was not found, `null` is returned.
	 *
	 * ```js
	 * "this is a sentence".find_first("s") == 3
	 * "this is a sentence".find_first("t") == 0
	 * "this is a sentence".find_first("x") == null
	 * ```
	 */
	public static function string_find_first(StringValue $haystack, AbstractValue $needle): AbstractValue {

		// Allow only some value types.
		Func::allow_argument_types(1, $needle, StringValue::class, NumberValue::class);

		$pos = \mb_strpos($haystack->value, (string) $needle->value);
		if ($pos !== \false) {
			return NumberValue::build((string) $pos, true);
		} else {
			return NullValue::build();
		}

	}

	/**
	 * Returns the position _(index)_ of **last** occurrence of `needle` in
	 * the `string`. If the `needle` was not found, `null` is returned.
	 *
	 * ```js
	 * "this is a sentence".find_first("s") == 3
	 * "this is a sentence".find_first("t") == 0
	 * "this is a sentence".find_first("x") == null
	 * ```
	 */
	public static function string_find_last(StringValue $haystack, AbstractValue $needle): AbstractValue {

		// Allow only some value types.
		Func::allow_argument_types(1, $needle, StringValue::class, NumberValue::class);

		$pos = \mb_strrpos($haystack->value, (string) $needle->value);
		if ($pos !== \false) {
			return NumberValue::build((string) $pos, true);
		} else {
			return NullValue::build();
		}

	}

	/**
	 * Join items from `iterable` with this `string` and return the result as
	 * a new string.
	 *
	 * ```js
	 * ','.join(['a', 'b', 3]) == "a,b,3"
	 * ':::'.join({'a': 1, 'b': 2, 'c': '3'}) == "1:::2:::3"
	 * '-PADDING-'.join("abc") == "a-PADDING-b-PADDING-3" // String is also iterable.
	 * ```
	 */
	public static function string_join(
		StringValue $string,
		AbstractValue $iterable
	): StringValue {

		$iter = $iterable->getIterator();
		if ($iter === null) {
			$type = $iterable::TYPE;
			throw new RuntimeError("Cannot join unsupported type '$type'");
		}

		$prepared = [];

		foreach ($iter as $item) {
			switch (\true) {
				case $item instanceof DictValue:
					$prepared[] = self::string_join($string, $item)->value;
					break;
				case $item instanceof ListValue:
						$prepared[] = self::string_join($string, $item)->value;
					break;
				default:
					$prepared[] = $item->getStringValue();
					break;
			}
		}

		return StringValue::build(\implode($string->value, $prepared));

	}

}
