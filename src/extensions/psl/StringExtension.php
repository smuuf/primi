<?php

namespace Smuuf\Primi\Psl;

use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Structures\FnContainer;
use \Smuuf\Primi\Structures\LazyValue;
use \Smuuf\Primi\Structures\StringValue;
use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\Structures\ArrayValue;
use \Smuuf\Primi\Structures\RegexValue;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\ErrorException;

class StringExtension extends Extension {

	public static function length() {

		return new LazyValue(
			FnContainer::buildFromClosure(function(StringValue $self): NumberValue {
				return new NumberValue(mb_strlen($self->value));
			})
		);

	}

	public static function format() {

		return function(StringValue $self, Value ...$items): StringValue {

			// Extract PHP values from passed in value objects, because later we will pass the values to sprintf().
			\array_walk($items, function(&$i) {
				$i = $i->value;
			});

			$count = \count($items);

			// We need to count how many non-positional placeholders are currently used, so we know
			// when to throw an error.
			$used = 0;

			// Convert {} syntax to a something sprintf() understands.
			// {} will be converted to "%s"
			// Positional {456} will be converted to "%456$s"
			$prepared = \preg_replace_callback("#\{(\d+)?\}#", function($match) use ($count, &$used) {

				if (isset($match[1])) {
					if ($match[1] > $count) {
						throw new ErrorException(
							sprintf("Position (%s) does not match the number of parameters (%s).", $match[1], $count)
						);
					}
					return "%{$match[1]}\$s";
				}

				if (++$used > $count) {
					throw new ErrorException(
						sprintf("Not enough parameters (%s) to match placeholder count (%s).", $count, $used)
					);
				}

				return "%s";

			}, $self->value);

			return new StringValue(\sprintf($prepared, ...$items));

		};

	}

	public static function replace() {

		return function(StringValue $self, Value $search, StringValue $replace = \null): StringValue {

			// Replacing using array of search-replace pairs.
			if ($search instanceof ArrayValue) {

				$from = \array_keys($search->value);

				// Values in ArrayValues are stored as Value objects,
				// so we need to extract the real PHP values from it.
				$to = \array_values(\array_map(function($item) {
					return $item->value;
				}, $search->value));

				return new StringValue(\str_replace($from, $to, $self->value));

			}

			if ($replace === \null) {
				throw new \ArgumentCountError;
			}

			if ($search instanceof StringValue || $search instanceof NumberValue) {

				// Handle both string/number values the same way.
				return new StringValue(\str_replace((string) $search->value, $replace->value, $self->value));

			} elseif ($search instanceof RegexValue) {
				return new StringValue(\preg_replace($search->value, $replace->value, $self->value));
			} else {
				throw new \TypeError;
			}

		};

	}

	public static function reverse() {

		return function(StringValue $self): StringValue {

			// strrev() does not support multibyte.
			// Let's do it ourselves then!

			$result = '';
			$len = mb_strlen($self->value);

			for ($i = $len; $i-- > 0;) {
				$result .= mb_substr($self->value, $i, 1);
			}

			return new StringValue($result);

		};

	}

	public static function split() {

		return function(StringValue $self, Value $delimiter): ArrayValue {

			// Allow only some value types.
			Common::allowTypes($delimiter, StringValue::class, RegexValue::class);

			if ($delimiter instanceof RegexValue) {
				$splat = preg_split($delimiter->value, $self->value);
			}

			if ($delimiter instanceof StringValue) {
				$splat = explode($delimiter->value, $self->value);
			}

			return new ArrayValue(array_map(function($part) {
				return new StringValue($part);
			}, $splat ?? []));

		};

	}

	public static function count() {

		return function(StringValue $self, Value $needle): NumberValue {

			// Allow only some value types.
			Common::allowTypes($needle, StringValue::class, NumberValue::class);

			return new NumberValue(\mb_substr_count($self->value, $needle->value));

		};

	}

	public static function first() {

		return function(StringValue $self, Value $needle): Value {

			// Allow only some value types.
			Common::allowTypes($needle, StringValue::class, NumberValue::class);

			$pos = \mb_strpos($self->value, (string) $needle->value);
			if ($pos !== \false) {
				return new NumberValue($pos);
			} else {
				return new BoolValue(\false);
			}

		};

	}

	public static function last() {

		return function(StringValue $self, Value $needle): Value {

			// Allow only some value types.
			Common::allowTypes($needle, StringValue::class, NumberValue::class);

			$pos = \mb_strrpos($self->value, (string) $needle->value);
			if ($pos !== \false) {
				return new NumberValue($pos);
			} else {
				return new BoolValue(\false);
			}

		};

	}

}
