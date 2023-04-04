<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use Smuuf\Primi\Scope;
use Smuuf\Primi\Context;
use Smuuf\Primi\StackFrame;
use Smuuf\Primi\Ex\TypeError;
use Smuuf\Primi\Ex\EngineError;
use Smuuf\Primi\Ex\BaseException;
use Smuuf\Primi\Code\Bytecode;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Values\TypeValue;
use Smuuf\Primi\Values\NumberValue;
use Smuuf\Primi\Values\AbstractValue;

abstract class Func {

	/**
	 * Pair of regexes to match zeroes at the beginning and at the end of a
	 * string, if they're not the last zeroes on that side of decimal point.
	 *
	 * @const string[][]
	 */
	private const DECIMAL_TRIMMING_REGEXES = [
		['#^(-?)0+(\d)#S', '#(\.0+$)|((\.\d+?[1-9]?)0+$)#S'],
		['\1\2', '\3']
	];

	/**
	 * Returns a generator yielding `[primi key, primi value]` tuples from some
	 * PHP array. If the value is not an instance of `AbstractValue`
	 * object, it will be converted automatically to a `AbstractValue` object.
	 *
	 * @param array<mixed, mixed> $array
	 * @return array<mixed>
	 * @phpstan-return TypeDef_PrimiObjectCouples
	 */
	public static function array_to_couples(array $array): iterable {

		foreach ($array as $key => $value) {
			yield [
				AbstractValue::buildAuto($key),
				AbstractValue::buildAuto($value)
			];
		}

	}

	public static function is_round_int(string $input): bool {
		return (bool) \preg_match('#^[+-]?\d+(\.0+)?$#S', $input);
	}

	public static function is_decimal(string $input): bool {
		return (bool) \preg_match('#^[+-]?\d+(\.\d+)?$#S', $input);
	}

	/**
	 * Returns a 8 character long hash unique for any existing PHP object and
	 * which is always the same for a specific PHP object instance.
	 *
	 * This is based on `spl_object_hash` but is visibly more "random" than what
	 * `spl_object_hash`.
	 *
	 * As is the case with `spl_object_hash`, a hash can be reused by a new
	 * object if the previous object with the same hash was destroyed during
	 * the PHP runtime.
	 */
	public static function object_hash(object $o): string {
		return \substr(\md5(\spl_object_hash($o)), 0, 8);
	}

	/**
	 * Return a 8 character long hash for any string.
	 *
	 * This hash should be used for "information" purposes - for example
	 * to help a quick by-human-eye comparison that two things are different.
	 */
	public static function string_hash(string $o): string {
		return \substr(\md5($o), 0, 8);
	}

	/**
	 * Normalize decimal number - trim zeroes from left and from right and
	 * do it, like, smart-like.
	 *
	 * Examples:
	 * - `00100.0` -> `100.0`
	 * - `00100.000` -> `100.0`
	 * - `00100.000100` -> `100.0001`
	 * - `000.000100` -> `0.0001`
	 * - `0100` -> `100`
	 * - `+0100` -> `100`
	 * - `-0100` -> `-100`
	 */
	public static function normalize_decimal(string $decimal): string {

		return \preg_replace(
			self::DECIMAL_TRIMMING_REGEXES[0],
			self::DECIMAL_TRIMMING_REGEXES[1],
			\ltrim(\trim($decimal), '+')
		);

	}

	/**
	 * Converts a number represented with scientific notation to a decimal
	 * number which is returned as a string.
	 *
	 * If there's not a decimal point nor an exponent present in the
	 * number, or even if the `$number` is not really a number, the original
	 * value is returned.
	 *
	 * Examples:
	 * `1.123E+6` -> `1123000`
	 * `987654.123E-6` -> `0.98765412`
	 * `987654.123` -> `987654.123`
	 * `987654` -> `987654`
	 * `not a number, bruh` -> `not a number, bruh`
	 */
	public static function scientific_to_decimal(string $number): string {

		// If not even in correct scientific form point, just return the
		// original.
		if (!\preg_match(
				"#^([+-]?\d+\.\d+)(?:E([+-]\d+))?$#S",
				$number,
				$matches
			)
		) {
			return $number;
		}

		// If there's no exponent, just return the original.
		if (!isset($matches[2])) {
			return $number;
		}

		// Otherwise, take the base and multiply it by the exponent.
		$decimal = $matches[1];
		$exp = $matches[2];
		return \bcmul(
			$decimal,
			\bcpow('10', $exp, NumberValue::PRECISION),
			NumberValue::PRECISION
		);

	}

	/**
	 * Helper for easy type-checking inside Primi extensions.
	 *
	 * Given an argument index, its value as object, and allowed types (as class
	 * names) as the rest of the arguments, this function either throws a
	 * TypeError exception with a user-friendly message or doesn't do
	 * anything.
	 *
	 * @param class-string|AbstractValue $allowedTypes
	 * @throws TypeError
	 */
	public static function allow_argument_types(
		int $pos,
		AbstractValue $arg,
		...$allowedTypes
	): void {

		// If any of the "instanceof" checks is true,
		// the type is allowed - return without throwing exception.
		foreach ($allowedTypes as $type) {

			if (\is_string($type) && $arg instanceof $type) {
				return;
			} elseif (
				$type instanceof TypeValue
				&& $arg->getType() === $type
			) {
				return;
			}

		}

		Exceptions::piggyback(
			StaticExceptionTypes::getTypeErrorType(),
			\sprintf(
				"Expected '%s' but got '%s' as argument %d",
				Types::php_classes_to_primi_types($allowedTypes),
				$arg->getTypeName(),
				$pos
			),
		);

	}

	/**
	 * @param array<string, AbstractValue|null> $current
	 * @param array<string, Bytecode> $defaults
	 * @return array<string, AbstractValue>
	 */
	public static function resolve_default_args(
		array $current,
		array $defaults,
		Context $ctx,
	): array {

		// Go through each of the known "defaults" for parameters and if its
		// corresponding current argument is not yet defined, use that
		// default's value definition (here presented as a AST node which we
		// can execute - which is done at call-time) to fetch the
		// argument's value.
		foreach ($defaults as $name => $code) {

			if (!empty($current[$name])) {
				continue;
			}

			$frame = $ctx->buildFrame(
				name: "<default: $name>",
				bytecode: $code,
			);

			$current[$name] = $ctx->runFrame($frame);

		}

		return $current;

	}

	/**
	 * Takes array representing AST node and makes sure that its contents are
	 * represented in a form of indexed sub-arrays. This comes handy if we want
	 * to be sure that multiple AST sub-nodes (which PHP-PEG parser returns) are
	 * universally iterable.
	 *
	 * @param array $node
	 * @phpstan-param TypeDef_AstNode $node
	 * @return TypeDef_AstNode
	 */
	public static function ensure_indexed(array $node): array {
		return !isset($node[0]) ? [$node] : $node;
	}

	/**
	 * Return a `[line, pos]` tuple for given (probably multiline) string and
	 * some offset.
	 *
	 * @return array{int, int}
	 */
	public static function get_position_estimate(string $string, int $offset): array {

		$substring = \mb_substr($string, 0, $offset);

		// Current line number? Just count the newline characters up to the offset.
		$line = \substr_count($substring, "\n") + 1;

		// Position on the current line? Just count how many characters are there
		// from the substring's end back to the latest newline character. If there
		// were no newline characters (mb_strrchr() returns false), the source code
		// is a single line and in that case the position is determined simply by
		// our substring's length.
		$lastLine = \mb_strrchr($substring, "\n");
		$pos = $lastLine === \false
			? \mb_strlen($substring)
			: \mb_strlen($lastLine);

		return [$line, $pos];

	}

	/**
	 * Helper function for easier left-to-right compilation of various abstract
	 * trees representing logical/mathematical operations.
	 *
	 * Returns a generator yielding tuples of `[operator string, operand node]`
	 * with the exception of first iteration, where the
	 * couple `[null, first operand node]` is returned.
	 *
	 * For example when primi source code `1 and 2 and 3` is parsed and then
	 * represented in a similar way to...
	 *
	 * ```php
	 * [
	 * 'ops' => ['Operator AND #1', 'Operator AND #2']
	 * 'operands' => [
	 *   <AST node for number 1',
	 *   <AST node for number 2',
	 *   <AST node for number 3',
	 * ]
	 * ]
	 * ```
	 *
	 * ... the generator will yield (in this order):
	 * - `[null, <AST node for number 1']`
	 * - `['Operator AND #1', <AST node for number 2']`
	 * - `['Operator AND #2', <AST node for number 3']`
	 *
	 * @param array $node
	 * @phpstan-param TypeDef_AstNode $node
	 * @return \Generator<array{string|null, AbstractValue}>
	 */
	public static function yield_nodes_left_to_right(array $node) {

		foreach ($node['operands'] as $i => $operand) {

			// First operator will be null.
			$operator = $node['ops'][$i - 1]['text'] ?? \null;
			if (yield [$operator, $operand]) {
				break;
			}

		}

	}

	/**
	 * Return best available time for measuring things - as seconds.
	 */
	public static function monotime(): float {
		return \hrtime(\true) / 1e9; // Nanoseconds to seconds.
	}

	/**
	 * Return a random, hopefully quite unique string.
	 */
	public static function unique_id(): string {
		return md5(random_bytes(128));
	}

	/**
	 * Takes an array list of strings and returns array list of strings that are
	 * guaranteed to represent a "realpath" to a directory in filesystem.
	 *
	 * If any of the passed strings is NOT a directory, `EngineError` is thrown.
	 *
	 * @param array<string> $paths
	 * @return array<string>
	 */
	public static function validate_dirs(array $paths): array {

		$result = [];
		foreach ($paths as &$path) {

			// Checked directory paths will be converted to "realpaths" -
			// ie. absolute paths.
			$rp = \realpath($path);
			if ($rp === \false || !is_dir($rp)) {
				throw new EngineError("Path '$path' is not a valid directory");
			}

			$result[] = rtrim($rp, '/');

		}

		return $result;

	}

	public static function joinObjectsAsString(
		array $objects,
		string $sep = ''
	): string {

		return implode(
			$sep,
			array_map(
				fn(AbstractValue $o) => $o->getStringValue(),
				$objects,
			)
		);

	}

	/**
	 * Is string a "dunder" (double-underscored) name? Dunder starts and ends
	 * with a double-underscore. For example `__init__` is a dunder.
	 */
	public static function is_dunder_name(string $input): bool {
		return \str_starts_with($input, '__') && \str_ends_with($input, '__');
	}

	/**
	 * Is string an "under" (underscored) name?
	 */
	public static function is_under_name(string $input): bool {
		return \str_starts_with($input, '_');
	}

}
