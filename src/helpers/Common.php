<?php

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\InternalArgumentTypeErrorException;
use \Smuuf\Primi\Structures\Value;

abstract class Common extends \Smuuf\Primi\StrictObject {

	/**
	 * Returns false if the passed array has contignuous numeric keys starting
	 * from 0 (i.e. it is a "list"). Returns true otherwise (i.e. it is a
	 * "dictionary).
	 *
	 * The solution was chosen based on 'array_list_dict' phpcb benchmark.
	 */
	public static function isArrayDict(array $input): bool {

		// Let's say that empty PHP array is not a dictionary.
		if (!$input) {
			return false;
		}

		$c = 0;
		foreach ($input as $i => $_) {
			if ($c++ !== $i) {
				return false;
			}
		}

		return true;

	}

	public static function isNumericInt(string $input): bool {
		// Solution based on 'numeric_int' phpcb benchmark results.
		return (bool) preg_match("#^[+-]?\d+$#", $input);
	}

	public static function isNumeric(string $input): bool {
		return (bool) \preg_match('#^[+-]?\d+(\.\d+)?$#', $input);
	}

	public static function objectHash($o): string {
		return substr(md5(spl_object_hash($o)), 0, 6);
	}

	/**
	 * Return true if the value passed as first argument is any of the types
	 * passed as the rest of variadic arguments.
	 *
	 * We're using this helper e.g. in value methods for performing easy
	 * checks against allowed set of types of values. If PHP ever supports union
	 * types, I guess this helper method might become unnecessary (?).
	 *
	 */
	public static function isAnyOfTypes(?Value $value, string ...$types): bool {

		// If any of the "instanceof" checks is true,
		// the type is allowed - return without throwing exception.
		foreach ($types as $type) {
			if ($value instanceof $type) {
				return true;
			}
		}

		return false;

	}

	/**
	 * Helper for easy type-checking inside Primi extensions.
	 * Checks if a N-th parameter value is of a certain allowed type(s) and
	 * throws a InternalArgumentTypeErrorException if it's not.
	 * The exception is handled by Primi's function-invoking logic and converted
	 * into a user-readable error.
	 */
	public static function allowArgumentTypes(
		int $index,
		Value $arg,
		string ...$allowedTypes
	) {

		// If any of the "instanceof" checks is true,
		// the type is allowed - return without throwing exception.
		foreach ($allowedTypes as $type) {
			if ($arg instanceof $type) {
				return;
			}
		}

		throw new InternalArgumentTypeErrorException(
			$index,
			$arg::TYPE,
			$allowedTypes
		);

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

	/**
	 * Returns array of Primi value types (PHP class names) of parameters
	 * for a PHP function of which the \ReflectionFunction is provided.
	 *
	 * In another words: This function returns which Primi types a PHP function
	 * expects.
	 */
	public static function getPrimiParameterTypesFromFunction(
		\ReflectionFunction $rf
	): array {

		$types = [];
		foreach ($rf->getParameters() as $rp) {

			$type = $rp->getType();

			// a) No typehint or b) typehint not hinting some Value class
			// means invalid type - gonna throw exception in that case.
			$invalidType = $type === null
				|| !is_a($type->getName(), Value::class, true);

			if ($invalidType) {

				$declClass = $rp->getDeclaringClass();
				$class = $declClass
					? $declClass->getName()
					: null;

				$method = $rp->getDeclaringFunction()->getName();
				$paramName = $rp->getName();
				$paramPosition = $rp->getPosition();
				$fqn = $class ? "{$class}::{$method}()" : "{$method}()";

				$msg = "Parameter '\${$paramName}' (#{$paramPosition}) of "
					. "{$fqn} doesn't have Primi value class typehint.";

				throw new ErrorException($msg);

			};

			$types[] = $type->getName();

		}

		return $types;

	}

}
