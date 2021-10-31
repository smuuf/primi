<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\StrictObject;

class StringEscaping {

	use StrictObject;

	/**
	 * Escape sequences that are supported in string literals.
	 *
	 * Left side represents character after a backslash which together form a
	 * supported escape sequence. Right side represents a result of such
	 * sequence.
	 *
	 * @const array
	 */
	private const ESCAPE_PAIRS = [
		'\\\\' => "\\",
		'\\n' => "\n",
		'\\t' => "\t",
		'\\"' => '"',
		"\\'" => "'",
		"\\e" => "\e",
	];

	private const QUOTE_CHARS = ['"', "'"];

	/**
	 * Return the provided string but with known escape sequences being expanded
	 * to their literal meaning. For example `\n` will be expanded to literal
	 * new-line.
	 */
	public static function unescapeString(string $str): string {

		return \preg_replace_callback('#(\\\\.)#', function($m) {

			$char = $m[1];
			foreach (self::ESCAPE_PAIRS as $in => $out) {
				if ($char === $in) {
					return $out;
				}
			}

			// The backslashed character doesn't represent any known escape
			// sequence, therefore error.
			throw new RuntimeError(
				"Unrecognized string escape sequence '{$m[0]}'."
			);

		}, $str);

	}

	/**
	 * The string provided as an argument will be returned, but with added
	 * escaping for characters that represent a known escape sequence. For
	 * example a literal new-line will be replaced by `\n`.
	 *
	 * This is useful for converting a internal string value of StringValue to
	 * a source code representaton of it - that is how a string literal would
	 * have to be written by hand for it to be - as a result - interpreted as
	 * that original string).
	 *
	 * If a third optional $quoteChar argument is passed, all other known
	 * quote characters will NOT be escaped - only the one specified by the
	 * third argument. The only known quote characters are `'` and `"`. For
	 * example the string `hello'there"` without $quoteChar specified will
	 * be escaped as `hello\'there\"`. But with $quoteChar being `"` it would
	 * result in `hello'there\"`, since the caller used the third argument to
	 * specify that the single-quote does NOT have to be escaped.
	 */
	public static function escapeString(
		string $str,
		string $quoteChar = \null
	): string {

		foreach (self::ESCAPE_PAIRS as $out => $in) {

			// $in = <new line>
			// $out = '\n'

			if (
				$quoteChar !== \null
				&& \in_array($in, self::QUOTE_CHARS, \true)
				&& $in !== $quoteChar
			) {
				// Do not escape quote characters that aren't necessary to be
				// escaped.
				continue;
			}

			$str = \str_replace($in, $out, $str);

		}

		return $str;

	}

}
