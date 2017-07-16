<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\UnsupportedOperationException;
use \Smuuf\Primi\ISupportsComparison;
use \Smuuf\Primi\ISupportsMultiplication;
use \Smuuf\Primi\ISupportsAddition;
use \Smuuf\Primi\ISupportsSubtraction;
use \Smuuf\Primi\ISupportsIteration;

class StringValue extends Value implements
	ISupportsAddition,
	ISupportsSubtraction,
	ISupportsIteration,
	ISupportsComparison
{

	const TYPE = "string";
	protected $splitCache;

	public function __construct(string $value) {
		$this->value = self::expandSequences($value);
	}

	public function doAddition(ISupportsAddition $rightOperand) {
		return new self($this->value . $rightOperand->value);
	}

	public function doSubtraction(ISupportsSubtraction $rightOperand) {

		if ($rightOperand instanceof NumberValue) {
			throw new UnsupportedOperationException;
		}

		return new self(str_replace($rightOperand->value, null, $this->value));

	}

	public function doComparison(string $op, ISupportsComparison $rightOperand) {

		switch ($op) {
			case "==":

				if ($rightOperand instanceof RegexValue) {
					$result = \preg_match($rightOperand->value, $this->value);
				} else {
					$result = $this->value === $rightOperand->value;
				}

				return new BoolValue($result);

			case "!=":
				return new BoolValue($this->value !== $rightOperand->value);
			default:
				throw new UnsupportedOperationException;
		}

	}

	public function getIterator(): \Iterator {
		return $this->splitCache ?: $this->splitCache = self::utfSplit($this->value);
	}

	// Helpers

	protected static function expandSequences(string $string) {

		// Primi strings support some escape sequences.
		$string = str_replace('\n', "\n", $string);
		return $string;

	}

	private static function utfSplit($string) {
		$strlen = mb_strlen($string);
		while ($strlen) {
			yield new self(mb_substr($string, 0, 1, "UTF-8"));
			$string = mb_substr($string, 1, $strlen, "UTF-8");
			$strlen = mb_strlen($string);
		}
	}

}
