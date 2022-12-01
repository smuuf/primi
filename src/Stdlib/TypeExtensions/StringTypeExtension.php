<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use \Smuuf\Primi\Extensions\PrimiFunc;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Stdlib\BuiltinTypes;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Values\DictValue;
use \Smuuf\Primi\Values\ListValue;
use \Smuuf\Primi\Values\RegexValue;
use \Smuuf\Primi\Values\TypeValue;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Extensions\TypeExtension;
use \Smuuf\Primi\Structures\CallArgs;

class StringTypeExtension extends TypeExtension {

	private const ATTR_DIGITS  = '0123456789';
	private const ATTR_LETTERS_LOWERCASE = 'abcdefghijklmnopqrstuvwxyz';
	private const ATTR_LETTERS_UPPERCASE = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	private const ATTR_LETTERS_ALL =
		self::ATTR_LETTERS_LOWERCASE . self::ATTR_LETTERS_UPPERCASE;

	public static function execute(): array {

		$attrs = [
			'ascii_letters' => Interned::string(self::ATTR_LETTERS_LOWERCASE),
			'ascii_lowercase' => Interned::string(self::ATTR_LETTERS_UPPERCASE),
			'ascii_uppercase' => Interned::string(self::ATTR_LETTERS_ALL),
			'digits' => Interned::string(self::ATTR_DIGITS),
		];

		return $attrs + parent::execute();

	}

	#[PrimiFunc(callConv: PrimiFunc::CONV_NATIVE)]
	public static function __new__(
		TypeValue $type,
		?AbstractValue $value = \null
	): StringValue {

		if ($type !== BuiltinTypes::getStringType()) {
			throw new TypeError("Passed invalid type object");
		}

		if ($value === \null) {
			return Interned::string('');
		}

		return Interned::string($value->getStringValue());

	}

	/**
	 * Returns a new `string` from shuffled characters of the original `string`.
	 *
	 * ```js
	 * "hello".shuffle() // "leohl" or something similar.
	 * ```
	 */
	#[PrimiFunc]
	public static function shuffle(StringValue $str): StringValue {

		// str_shuffle() doesn't work with unicode, so let's do this ourselves.
		$original = $str->value;
		$length = \mb_strlen($original);
		$indices = \range(0, $length - 1);
		\shuffle($indices);
		$result = "";

		while (($i = \array_pop($indices)) !== \null) {
			$result .= \mb_substr($original, $i, 1);
		}

		return Interned::string($result);

	}

	/**
	 * Returns a new `string` with placeholders from the original `string`
	 * replaced by additional arguments.
	 *
	 * Placeholders can be either _(but these can't be combined)_:
	 *   - Non-positional: `{}`
	 *   - Positional: `{0}`, `{1}`, `{2}`, etc.
	 *
	 * ```js
	 * "x{}x, y{}y".format(1, 2) == "x1x, y2y"
	 * "x{1}x, y{0}y".format(111, 222) == "x222x, y111y"
	 * ```
	 */
	#[PrimiFunc]
	public static function format(
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
				$index = (int) $m[1];

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

		return Interned::string(\sprintf($prepared, ...$items));

	}

	/**
	 * Perform search and replace and return the results as new `string`.
	 *
	 * ```js
	 * "abcdef".replace("c", "X") == "abXdef"
	 * "přítmí ve městě za dvě stě".replace("stě", "šci") == "přítmí ve měšci za dvě šci"
	 * "přítmí ve městě za dvě stě".replace(rx"\wt\w", "lol") == "přlolí ve mělol za dvě lol"
	 * ```
	 *
	 */
	#[PrimiFunc]
	public static function replace(
		StringValue $string,
		AbstractValue $search,
		StringValue $replace
	): StringValue {

		if ($search instanceof StringValue || $search instanceof NumberValue) {

			// Handle both string/number values the same way.
			return Interned::string(
				\str_replace(
					(string) $search->value,
					$replace->value,
					$string->value
				)
			);

		} elseif ($search instanceof RegexValue) {
			return Interned::string(
				\preg_replace(
					$search->value,
					$replace->value,
					$string->value
				)
			);
		} else {

			$type = $search->getTypeName();
			throw new RuntimeError("Cannot use '$type' as needle");

		}

	}

	/**
	 * Search and replace strings within a string and return the new resulting
	 * string. The from-to pairs are to be provided as a `dict`.
	 *
	 * ```js
	 * "abcdef".replace({'c': 'X', 'e': 'Y'}) == "abXdYf"
	 * "abcdef".replace({'b': 'X', 'ab': 'Y'}) == "Ycdef"
	 * ```
	 *
	 * The longest keys will be tried first. Once a substring has been replaced,
	 * its new value will not be searched again. This behavior is identical
	 * to PHP function [`strtr()`](https://www.php.net/manual/en/function.strtr.php).
	 *
	 */
	#[PrimiFunc]
	public static function translate(
		StringValue $string,
		DictValue $pairs
	): StringValue {

		$mapping = [];
		$c = 0;

		// Extract <from: to> pairs from the dict.
		foreach ($pairs->value->getItemsIterator() as [$key, $value]) {

			if (!$key instanceof StringValue) {
				$type = $key->getTypeName();
				throw new RuntimeError("Replacement dict key must be a string, '$type' given.");
			}

			if (!$value instanceof StringValue) {
				$type = $value->getTypeName();
				throw new RuntimeError("Replacement dict value must be a string, '$type' given.");
			}

			$mapping[$key->value] = $value->value;
			$c++;

		}

		return Interned::string(\strtr($string->value, $mapping));

	}

	/**
	 * Return reversed string.
	 *
	 * ```js
	 * "hello! tady čaj".reverse() == "jač ydat !olleh"
	 * ```
	 *
	 */
	#[PrimiFunc]
	public static function reverse(StringValue $string): StringValue {

		// strrev() does not support multibyte.
		// Let's do it ourselves then!

		$result = '';
		$len = \mb_strlen($string->value);

		for ($i = $len; $i-- > 0;) {
			$result .= \mb_substr($string->value, $i, 1);
		}

		return Interned::string($result);

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
	#[PrimiFunc(callConv: PrimiFunc::CONV_CALLARGS)]
	public static function split(
		CallArgs $args
	): ListValue {

		$args = $args->extract(
			['self', 'delimiter', 'limit'],
			['delimiter', 'limit']
		);

		// Split by whitespaces by default.
		$self = $args['self'];
		$delimiter = $args['delimiter'] ?? Interned::regex('\s+');
		$limit = $args['limit'] ?? Interned::number('-1');

		// Allow only some value types.
		Func::allow_argument_types(1, $self, StringValue::class);
		Func::allow_argument_types(2, $delimiter, StringValue::class, RegexValue::class);
		Func::allow_argument_types(3, $limit, NumberValue::class);

		if ($delimiter instanceof RegexValue) {
			$splat = \preg_split($delimiter->value, $self->value, (int) $limit->value);
		}

		if ($delimiter instanceof StringValue) {
			if ($delimiter->value === '') {
				throw new RuntimeError("String delimiter must not be empty.");
			}
			$splat = \explode($delimiter->value, $self->value, (int) $limit->value);
		}

		return new ListValue(\array_map(function($part) {
			return Interned::string($part);
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
	#[PrimiFunc]
	public static function contains(
		StringValue $haystack,
		AbstractValue $needle
	): BoolValue {
		return Interned::bool($haystack->doesContain($needle));
	}

	/**
	 * Returns `number` of occurrences of `needle` in a string.
	 *
	 * ```js
	 * "this is a sentence".number_of("s") == 3
	 * "this is a sentence".number_of("x") == 0
	 * ```
	 */
	#[PrimiFunc]
	public static function number_of(
		StringValue $haystack,
		AbstractValue $needle
	): NumberValue {

		// Allow only some value types.
		Func::allow_argument_types(1, $needle, StringValue::class);

		$count = (string) \mb_substr_count(
			(string) $haystack->value,
			(string) $needle->value
		);

		return Interned::number($count);

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
	#[PrimiFunc]
	public static function find_first(StringValue $haystack, AbstractValue $needle): AbstractValue {

		// Allow only some value types.
		Func::allow_argument_types(1, $needle, StringValue::class, NumberValue::class);

		$pos = \mb_strpos($haystack->value, (string) $needle->value);
		if ($pos !== \false) {
			return Interned::number((string) $pos);
		} else {
			return Interned::null();
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
	#[PrimiFunc]
	public static function find_last(
		StringValue $haystack,
		AbstractValue $needle
	): AbstractValue {

		// Allow only some value types.
		Func::allow_argument_types(1, $needle, StringValue::class, NumberValue::class);

		$pos = \mb_strrpos($haystack->value, (string) $needle->value);
		if ($pos !== \false) {
			return Interned::number((string) $pos);
		} else {
			return Interned::null();
		}

	}

	/**
	 * Join items from `iterable` with this `string` and return the result as
	 * a new string.
	 *
	 * ```js
	 * ','.join(['a', 'b', 3]) == "a,b,3"
	 * ':::'.join({'a': 1, 'b': 2, 'c': '3'}) == "1:::2:::3"
	 * '-PADDING-'.join("abc") == "a-PADDING-b-PADDING-c" // String is also iterable.
	 * ```
	 */
	#[PrimiFunc(toStack: \true)]
	public static function join(
		StringValue $string,
		AbstractValue $iterable
	): StringValue {

		$iter = $iterable->getIterator();
		if ($iter === \null) {
			$type = $iterable->getTypeName();
			throw new RuntimeError("Cannot join unsupported type '$type'");
		}

		$prepared = [];

		foreach ($iter as $item) {
			switch (\true) {
				case $item instanceof DictValue:
					$prepared[] = self::join($string, $item)->value;
					break;
				case $item instanceof ListValue:
						$prepared[] = self::join($string, $item)->value;
					break;
				default:
					$prepared[] = $item->getStringValue();
					break;
			}
		}

		return Interned::string(\implode($string->value, $prepared));

	}

	/**
	 * Returns `true` if the string starts with specified string.
	 *
	 * ```js
	 * "this is a sentence".starts_with("tence") == true
	 * "this is a sentence".starts_with("e") == true
	 * "this is a sentence".starts_with("x") == false
	 * ```
	 */
	#[PrimiFunc]
	public static function starts_with(
		StringValue $haystack,
		StringValue $needle
	): BoolValue {
		return Interned::bool(\str_starts_with($haystack->value, $needle->value));
	}

	/**
	 * Returns `true` if the string ends with specified string suffix.
	 *
	 * ```js
	 * "this is a sentence".ends_with("tence") == true
	 * "this is a sentence".ends_with("e") == true
	 * "this is a sentence".ends_with("x") == false
	 * ```
	 */
	#[PrimiFunc]
	public static function ends_with(
		StringValue $haystack,
		StringValue $needle
	): BoolValue {
		return Interned::bool(\str_ends_with($haystack->value, $needle->value));
	}

}
