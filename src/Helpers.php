<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;

abstract class Helpers extends \Smuuf\Primi\StrictObject {

	/**
	 * Takes array as reference and ensures its contents are represented in a form of indexed sub-arrays.
	 * This comes handy if we want to be sure that multiple sub-nodes (which PHP-PEG parser returns) are universally
	 * iterable.
	 */
	public static function ensureIndexed(array &$array): void {

		if (!isset($array[0])) {
			$array = [$array];
		}

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
	 * Invoke the $method method with $args arguments on a $subject Value object.
	 *
	 * This method also provides the necessary stuffing around such invocation,
	 * such as catching different possible exceptions.
	 */
	public static function invokeValueMethod(Value $subject, string $method, array $args = [], array $node = []): Value {

		try {

			return $subject->call($method, $args);

		} catch (\ArgumentCountError $e) {

			// We have the counts of expected/passed arguments available,
			// add that information to the error message.
			$expectedCounts = self::parseArgumentCountError($e);
			throw new InternalArgumentCountException($method, $expectedCounts[0], $expectedCounts[1]);

		} catch (\TypeError $e) {

			// Make use of PHP's internal TypeError being thrown when passing wrong types of arguments.
			throw new ErrorException(sprintf("Wrong arguments passed to method '%s' of '%s'.", $method, $subject::TYPE), $node);

		} catch (InternalUndefinedMethodException $e) {

			throw new ErrorException(sprintf("Calling undefined method '%s' on '%s'.", $method, $subject::TYPE), $node);

		}

	}

	/**
	 * Parse \ArgumentCountError's message and return a tuple of integers
	 * representing:
	 * 1. Number of arguments passed.
	 * 2. Number of arguments expected.
	 */
	protected static function parseArgumentCountError(\ArgumentCountError $e): array {

		$msg = $e->getMessage();

		// ArgumentCountError exception does not provide these numbers itself,
		// so we have to extract it from the internal PHP exception message.
		if (!preg_match('#(?<passed>\d+)\s+passed.*(?<expected>\d+)\s+expected#', $msg, $m)) {
			return [];
		}

		// Also, because of how calling Primi value methods work, we need to
		// subtract 1 from these numbers. (first argument is the value - upon
		// which the method is called - itself).
		return [$m['passed'] - 1, $m['expected'] - 1];

	}

}
