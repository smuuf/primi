<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\ISupportsComparison;
use \Smuuf\Primi\ISupportsAddition;
use \Smuuf\Primi\ISupportsSubtraction;
use \Smuuf\Primi\ISupportsIteration;
use \Smuuf\Primi\ISupportsDereference;
use \Smuuf\Primi\ISupportsInsertion;
use \Smuuf\Primi\ErrorException;

class StringValue extends Value implements
	ISupportsAddition,
	ISupportsSubtraction,
	ISupportsIteration,
	ISupportsComparison,
	ISupportsInsertion,
	ISupportsDereference
{

	const TYPE = "string";

	public function __construct(string $value) {
		$this->value = self::expandSequences($value);
	}

	public function getStringValue(): string {

		// We are about to put double-quotes around the return value,
		// so let's "escape" double-quotes present in the string value.
		$escaped = str_replace('"', '\"', $this->value);
		return "\"$escaped\"";

	}

	public function doAddition(Value $rightOperand) {

		self::allowTypes($rightOperand, self::class, NumberValue::class);
		return new self($this->value . $rightOperand->value);

	}

	public function doSubtraction(Value $rightOperand) {

		if ($rightOperand instanceof RegexValue) {
			return new self(\preg_replace($rightOperand->value, \null, $this->value));
		}

		// Allow only string at this point (if the operand was a regex, we've already returned value).
		self::allowTypes($rightOperand, self::class);

		return new self(\str_replace($rightOperand->value, \null, $this->value));

	}

	public function doComparison(string $op, Value $rightOperand): BoolValue {

		self::allowTypes(
			$rightOperand,
			self::class,
			RegexValue::class,
			NumberValue::class
		);

		switch ($op) {
			case "==":

				if ($rightOperand instanceof RegexValue) {
					$result = \preg_match($rightOperand->value, $this->value);
				} else {
					$result = $this->value === (string) $rightOperand->value;
				}

			break;
			case "!=":

				if ($rightOperand instanceof RegexValue) {
					$result = !\preg_match($rightOperand->value, $this->value);
				} else {
					$result = $this->value !== (string) $rightOperand->value;
				}

			break;
			default:
				throw new \TypeError;
		}

		return new BoolValue($result);

	}

	public function dereference(Value $index) {

		// Allow dereferencing only by numbers.
		self::allowTypes($index, NumberValue::class);

		$phpIndex = (string) $index->value;

		if (!isset($this->value[$phpIndex])) {
			throw new \Smuuf\Primi\InternalUndefinedIndexException($phpIndex);
		}

		return new self($this->value[$phpIndex]);

	}

	public function insert(?Value $index, Value $value): Value {

		// Allow only strings to be inserted.
		self::allowTypes($value, self::class);

		if ($index === \null) {

			// An empty index will cause the value to be appended to the end.
			$this->value .= $value->value;

		} else {

			// Only numbers can be indexes in strings.
			self::allowTypes($index, NumberValue::class);
			$phpIndex = $index->value;

			// If index is specified, PHP own rules for inserting into strings apply.
			$this->value[$phpIndex] = $value->value;

		}

		return $this;

	}

	public function getInsertionProxy(?Value $index): InsertionProxy {
		return new InsertionProxy($this, $index);
	}

	public function getIterator(): \Iterator {
		return self::utfSplit($this->value);
	}

	// Helpers

	protected static function expandSequences(string $string) {

		// Primi strings support some escape sequences.
		return \str_replace('\n', "\n", $string);

	}

	/**
	 * Return a generator yielding each of this string's characters as
	 * new one-character StringValue objects.
	 */
	private static function utfSplit(string $string): \Generator {

		$strlen = \mb_strlen($string);
		while ($strlen) {
			yield new self(\mb_substr($string, 0, 1, "UTF-8"));
			$string = \mb_substr($string, 1, $strlen, "UTF-8");
			$strlen = \mb_strlen($string);
		}

	}

	// Properties.

	public function propLength(): NumberValue {
		return new NumberValue(\mb_strlen($this->value));
	}

	// Methods

	public function callFormat(Value ...$items): self {

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

		}, $this->value);

		return new self(\sprintf($prepared, ...$items));

	}

	public function callReplace(Value $search, self $replace = \null): self {

		// Replacing using array of search-replace pairs.
		if ($search instanceof ArrayValue) {

			$from = \array_keys($search->value);

			// Values in ArrayValues are stored as Value objects, so we need to extract the real PHP values from it.
			$to = \array_values(\array_map(function($item) {
				return $item->value;
			}, $search->value));

			return new self(\str_replace($from, $to, $this->value));

		}

		if ($replace === \null) {
			throw new \TypeError;
		}

		if ($search instanceof self || $search instanceof NumberValue) {

			// Handle both string/number values the same way.
			return new self(\str_replace((string) $search->value, $replace->value, $this->value));

		} elseif ($search instanceof RegexValue) {
			return new self(\preg_replace($search->value, $replace->value, $this->value));
		} else {
			throw new \TypeError;
		}

	}

	public function callSplit(Value $delimiter): ArrayValue {

		// Allow only some value types.
		self::allowTypes($delimiter, self::class, RegexValue::class);

		if ($delimiter instanceof RegexValue) {
			$splat = preg_split($delimiter->value, $this->value);
		}

		if ($delimiter instanceof self) {
			$splat = explode($delimiter->value, $this->value);
		}

		return new ArrayValue(array_map(function($part) {
			return new self($part);
		}, $splat));

	}

	public function callCount(Value $needle): NumberValue {

		// Allow only some value types.
		self::allowTypes($needle, self::class, NumberValue::class);

		return new NumberValue(\mb_substr_count($this->value, $needle->value));

	}

	public function callFirst(Value $needle): Value {

		// Allow only some value types.
		self::allowTypes($needle, self::class, NumberValue::class);

		$pos = \mb_strpos($this->value, (string) $needle->value);
		if ($pos !== \false) {
			return new NumberValue($pos);
		} else {
			return new BoolValue(\false);
		}

	}

	public function callLast(Value $needle): Value {

		// Allow only some value types.
		self::allowTypes($needle, self::class, NumberValue::class);

		$pos = \mb_strrpos($this->value, (string) $needle->value);
		if ($pos !== \false) {
			return new NumberValue($pos);
		} else {
			return new BoolValue(\false);
		}

	}

}
