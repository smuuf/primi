<?php

namespace Smuuf\Primi;

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

}
