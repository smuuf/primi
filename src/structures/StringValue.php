<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Stl\StringLibrary;

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

	protected static $libraries = [
		StringLibrary::class,
	];

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


}
