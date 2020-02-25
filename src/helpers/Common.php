<?php

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Structures\StringValue;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Structures\NullValue;
use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\Structures\ArrayValue;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\InternalUndefinedTruthnessException;

abstract class Common extends \Smuuf\Primi\StrictObject {

	public static function isTruthy(Value $value): bool {

		$v = $value->getInternalValue();

		switch (true) {
			case $value instanceof StringValue:
				return $v !== "";
			case $value instanceof BoolValue:
				return $v;
			case $value instanceof NumberValue:
				return (bool) $v;
			case $value instanceof NullValue:
				return false;
			case $value instanceof ArrayValue:
				return (bool) $v;
		}

		$msg = sprintf("Cannot determine truthness of type '%s'", $value::TYPE);
		throw new InternalUndefinedTruthnessException($msg);

	}

	public static function objectHash($o): string {
		return substr(md5(spl_object_hash($o)), 0, 6);
	}

	/**
	 * Throw new TypeError when the value does not match any of the types
	 * provided.
	 *
	 * We're using this helper e.g. in value methods for performing easy
	 * checks against allowed set of types of values. If PHP ever supports union
	 * types, I guess this helper method might become unnecessary.
	 *
	 * @throws \TypeError
	 */
	public static function allowTypes(?Value $value, string ...$types) {

		// If any of the "instanceof" checks is true,
		// the type is allowed - return without throwing exception.
		foreach ($types as $type) {
			if ($value instanceof $type) {
				return;
			}
		}

		// The value did not match any of the types provided.
		$msg = sprintf(
			"'%s' is not any of these: %s",
			$value::TYPE,
			implode(", ", $types)
		);

		throw new \TypeError($msg);

	}

	/**
	 * Takes array as reference and ensures its contents are represented in a form of indexed sub-arrays.
	 * This comes handy if we want to be sure that multiple sub-nodes (which PHP-PEG parser returns) are universally
	 * iterable.
	 */
	public static function ensureIndexed(array $array): array {
		return !isset($array[0]) ? [$array] : $array;
	}

	public static function hash(...$args): string {
		return md5(json_encode($args));
	}

	public static function getPositionEstimate(string $string, int $offset): array {

		$substring = \mb_substr($string, 0, $offset);

		// Current line's number? Just count the newline characters up to the offset.
		$line = \substr_count($substring, "\n") + 1;

		// Position on the current line? Just count how many characters are there from the
		// substring's end back to the latest newline character.
		// If there were no newline characters (mb_strrchr() returns false), the source code is a
		// single line and in that case the position is determined simply by our substring's length.
		$lastLine = mb_strrchr($substring, "\n");
		$pos = $lastLine === false ? mb_strlen($substring) : \mb_strlen($lastLine);

		return [$line, $pos];

	}

	/**
	 * Parse \ArgumentCountError's message and return a tuple of integers
	 * representing:
	 * 1. Number of arguments passed.
	 * 2. Number of arguments expected.
	 */
	public static function parseArgumentCountError(\ArgumentCountError $e): array {

		$msg = $e->getMessage();

		// ArgumentCountError exception does not provide these numbers itself,
		// so we have to extract it from the internal PHP exception message.
		if (!preg_match('#(?<passed>\d+)\s+passed.*(?<expected>\d+)\s+expected#', $msg, $m)) {
			return [null, null];
		}

		return [$m['passed'], $m['expected']];

	}

}
