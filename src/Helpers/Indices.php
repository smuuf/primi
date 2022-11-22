<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Ex\IndexError;
use \Smuuf\Primi\Values\AbstractValue;

/**
 * Helpers for handling accessing PHP arrays via possibly negative indices.
 */
abstract class Indices {

	/**
	 * Translate negative index to positive index that's a valid index for a
	 * given number of items.
	 *
	 * Return `null` when it's not possible to do so.
	 *
	 * For example:
	 * - index 1 for list with 2 items -> index == 1
	 * - index 2 for list with 2 items -> NOT FOUND!
	 * - index -1 for list with 2 items -> index == <max_index> - 1 (= 1)
	 * - index -2 for list with 2 items -> index == <max_index> - 2 (= 0)
	 * - index -3 for list with 2 items -> NOT FOUND!
	 */
	public static function resolveNegativeIndex(
		int $index,
		int $maxIndex
	): ?int {

		$normalized = $index < 0
			? $maxIndex + $index + 1
			: $index;

		if ($normalized < 0 || $normalized > $maxIndex) {
			return \null;
		}

		return $normalized;

	}

	/**
	 * Return resolved positive array index based on possibly negative index
	 * passed as the first argument.
	 *
	 * If the negative index could not be resolved, or if the index does not
	 * represent an existing index in the array passed as the second argument,
	 * an IndexError exception is thrown.
	 *
	 * @param array<int, AbstractValue>|\ArrayAccess<int, AbstractValue> $array
	 * @return mixed
	 */
	public static function resolveIndexOrError(int $index, $array) {

		$actualIndex = self::resolveNegativeIndex($index, \count($array) - 1);
		if ($actualIndex === \null || !isset($array[$actualIndex])) {

			// $index on purpose - report error with the index originally used.
			throw new IndexError($index);

		}

		return $actualIndex;

	}

}
