<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\StackFrame;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Ex\EngineInternalError;
use \Smuuf\Primi\Parser\GrammarHelpers;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Structures\MapContainer;
use \Smuuf\Primi\Values\TypeValue;

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
	 * @return TypeDef_PrimiObjectCouples
	 */
	public static function array_to_couples(array $array): iterable {

		foreach ($array as $key => $value) {
			yield [
				AbstractValue::buildAuto($key),
				AbstractValue::buildAuto($value)
			];
		}

	}

	/**
	 * Convert iterable of couples _(PHP 2-tuple arrays with two items
	 * containing Primi objects, where first item must a string object
	 * representing a valid Primi variable name)_ to PHP dict array mapping
	 * pairs of `['variable_name' => Some Primi object]`.
	 *
	 * @param TypeDef_PrimiObjectCouples $couples
	 * @return array<string, AbstractValue> PHP dict array mapping of variables.
	 * @throws TypeError
	 */
	public static function couples_to_variables_array(
		iterable $couples,
		string $intendedTarget
	): array {

		$attrs = [];
		foreach ($couples as [$k, $v]) {

			if (!$k instanceof StringValue) {
				throw new TypeError(
					"$intendedTarget is not a string but '{$k->getTypeName()}'");
			}

			$varName = $k->getStringValue();
			if (!GrammarHelpers::isValidName($varName)) {
				throw new TypeError(
					"$intendedTarget '$varName' is not a valid name");
			}

			$attrs[$varName] = $v;

		}

		return $attrs;

	}

	/**
	 * Returns PHP iterable returning couples (2-tuples) of `[key, value]` from
	 * a iterable Primi object that can be interpreted as a mapping.
	 * Best-effort-style.
	 *
	 * @return TypeDef_PrimiObjectCouples
	 * @throws TypeError
	 */
	public static function mapping_to_couples(AbstractValue $value) {

		$internalValue = $value->getInternalValue();
		if ($internalValue instanceof MapContainer) {

			// If the internal value already is a mapping represented by
			// MapContainer, just return its items-iterator.
			return $internalValue->getItemsIterator();

		} else {

			// We can also try to extract mapping from Primi iterable objects.
			// If the Primi object provides an iterator, we're going to iterate
			// over its items AND if each of these items is an iterable with
			// two items in it, we can extract mapping from it - and convert
			// it into Primi object couples.

			// First, we try if the passed Primi object supports iteration.
			$items = $value->getIterator();
			if ($items === \null) {
				throw new TypeError("Unable to create mapping from non-iterable");
			}

			// We prepare the result container for couples, which will be
			// discarded if we encounter any errors when putting results in it.
			$couples = [];
			$i = -1;

			foreach ($items as $item) {

				$couple = [];
				$i++;
				$j = -1;

				// Second, for each of the item of the top-iterator we check
				// if the item also supports iteration.
				$subitems = $item->getIterator();
				if ($subitems === \null) {
					throw new TypeError(
						"Unable to create mapping from iterable: "
						. "item #$i is not iterable"
					);
				}

				foreach ($subitems as $subitem) {

					// Third, since we want to build and return iterable
					// containing couples, the item needs to contain
					// exactly two sub-items.
					if (++$j > 2) {
						throw new TypeError(
							"Unable to create mapping from iterable: "
							. "item #$i has more than two items ($j) in it"
						);
					}

					$couple[] = $subitem;

				}

				$couples[] = $couple;

			}

			// All went well, return iterable (list array) with all gathered
			// couples.
			return $couples;

		}

	}

	public static function is_round_int(string $input): bool {

		if (!\is_numeric($input)) {
			return \false;
		}

		return \round((float) $input) == $input; // Intentionally ==

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
	 * @param class-string|AbstractValue
	 * @throws TypeError
	 */
	public static function allow_argument_types(
		int $index,
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

		throw new TypeError(\sprintf(
			"Expected '%s' but got '%s' as argument %d",
			Types::php_classes_to_primi_types($allowedTypes),
			$arg->getTypeName(),
			$index
		));

	}

	/**
	 * @param array<string, AbstractValue|null> $current
	 * @param array<string, TypeDef_AstNode> $defaults
	 * @return array<string, AbstractValue>
	 */
	public static function resolve_default_args(
		array $current,
		array $defaults,
		Context $ctx
	): array {

		// Go through each of the known "defaults" for parameters and if its
		// corresponding current argument is not yet defined, use that
		// default's value definition (here presented as a AST node which we
		// can execute - which
		// is done at call-time) to fetch the argument's value.
		foreach ($defaults as $name => $astNode) {
			if (empty($current[$name])) {
				$current[$name] = HandlerFactory::runNode($astNode, $ctx);
			}
		}

		return $current;

	}

	/**
	 * Takes array representing AST node and makes sure that its contents are
	 * represented in a form of indexed sub-arrays. This comes handy if we want
	 * to be sure that multiple AST sub-nodes (which PHP-PEG parser returns) are
	 * universally iterable.
	 *
	 * @param TypeDef_AstNode $node
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
	 * Helper function for easier left-to-right evaluation of various abstract
	 * trees representing logical/mathematical operations.
	 *
	 * Returns a generator yielding tuples of `[operator, operand]` with the
	 * exception of first iteration, where the tuple `[null, first operand]` is
	 * returned.
	 *
	 * For example when primi source code `1 and 2 and 3` is parsed and then
	 * represented in a similar way to...
	 *
	 * ```php
	 * [
	 * 'operands' => ['Number 1', 'Number 2', 'Number 3']
	 * 'ops' => ['Operator AND #1', 'Operator AND #2']
	 * ]
	 * ```
	 *
	 * ... the generator will yield (in this order):
	 * - `[null, 'Number 1']`
	 * - `['Operator AND #1', 'Number 2']`
	 * - `['Operator AND #2', 'Number 3']`
	 *
	 * This way client code can, for example, implement short-circuiting by
	 * using the result so-far and not processing the rest of what the generator
	 * would yield.
	 *
	 * @param TypeDef_AstNode $node
	 * @return \Generator<array{string|null, AbstractValue}>
	 */
	public static function yield_left_to_right(array $node, Context $ctx) {

		foreach ($node['operands'] as $i => $operand) {

			// First operator will be null and the last one too.
			$operator = $node['ops'][$i - 1]['text'] ?? \null;
			$value = HandlerFactory::runNode($operand, $ctx);

			if (yield [$operator, $value]) {
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
	 * @param array<StackFrame> $callstack Callstack.
	 */
	public static function get_traceback_as_string(
		array $callstack,
		bool $withColors = false
	): string {

		$result = Colors::get("{yellow}Traceback:{_}\n", true, $withColors);

		foreach ($callstack as $level => $call)  {

			$result .= Colors::get(
				"{yellow}[$level]{_} {$call}\n",
				true,
				$withColors
			);

		}

		return $result;

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
