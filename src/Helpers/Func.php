<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Ex\EngineInternalError;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Structures\CallArgs;
use \Smuuf\Primi\Values\FuncValue;
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
	 * PHP array. If the key or the value is not an instance of `AbstractValue`
	 * object, it will be converted automatically to a `AbstractValue` object.
	 */
	public static function array_to_primi_value_tuples(array $array): \Generator {

		foreach (Func::iterator_as_tuples($array) as [$key, $value]) {
			yield [
				AbstractValue::buildAuto($key),
				AbstractValue::buildAuto($value)
			];
		}

	}

	/**
	 * Returns a generator yielding `[key, value]` tuples from some iterator.
	 */
	public static function iterator_as_tuples(iterable $iter): \Generator {
		foreach ($iter as $key => $value) {
			yield [$key, $value];
		}
	}

	/**
	 * Returns a generator yielding values from iterable.
	 *
	 * This works as `iterator_to_array`, but also supports generator iterables
	 * which use objects as keys (which `iterator_to_array` hates and throws
	 * the 'Illegal offset type' error).
	 */
	public static function get_map_values(iterable $iter): array {

		$result = [];
		foreach ($iter as $key => $value) {

			if ($key instanceof AbstractValue) {
				$key = $key->getStringValue();
			}

			$result[$key] = $value;

		}

		return $result;

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
	public static function object_hash($o): string {
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
	 * Return true if the value passed as first argument is any of the types
	 * passed as the rest of variadic arguments.
	 *
	 * We're using this helper e.g. in value methods for performing easy
	 * checks against allowed set of types of values. If PHP ever supports union
	 * types, I guess this helper method might become unnecessary (?).
	 *
	 */
	public static function is_any_of_types(?AbstractValue $value, string ...$types): bool {

		// If any of the "instanceof" checks is true,
		// the type is allowed - return without throwing exception.
		foreach ($types as $type) {
			if ($value instanceof $type) {
				return \true;
			}
		}

		return \false;

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
	 */
	public static function allow_argument_types(
		int $index,
		AbstractValue $arg,
		string ...$allowedTypes
	) {

		// If any of the "instanceof" checks is true,
		// the type is allowed - return without throwing exception.
		foreach ($allowedTypes as $type) {
			if ($arg instanceof $type) {
				return;
			}
		}

		throw new TypeError(\sprintf(
			"Expected '%s' but got '%s' as argument %d",
			Func::php_types_to_primi_types($allowedTypes),
			$arg->getTypeName(),
			$index
		));

	}

	/**
	 * Takes array as reference and ensures its contents are represented in a form
	 * of indexed sub-arrays. This comes handy if we want to be sure that multiple
	 * AST sub-nodes (which PHP-PEG parser returns) are universally iterable.
	 */
	public static function ensure_indexed(array $array): array {
		return !isset($array[0]) ? [$array] : $array;
	}

	/**
	 * Return a `[line, pos]` tuple for given (probably multiline) string and
	 * some offset.
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
	 * Returns array of Primi value types (PHP class names) of parameters
	 * for a PHP function of which the \ReflectionFunction is provided.
	 *
	 * In another words: This function returns which Primi types a PHP function
	 * expects.
	 */
	public static function check_allowed_parameter_types_of_function(
		\ReflectionFunction $rf
	): array {

		$types = [];
		foreach ($rf->getParameters() as $rp) {

			$invalid = \false;
			$type = $rp->getType();

			if ($type === \null) {
				$invalid = 'Type must be specified';
			} else {

				// See https://github.com/phpstan/phpstan/issues/3886#issuecomment-699599667
				if (!$type instanceof \ReflectionNamedType) {
					$invalid = "Union types not yet supported";
				} else {

					$typeName = $type->getName();

					// a) Invalid if not hinting some AbstractValue class or its descendants.
					// b) Invalid if not hinting the Context class.
					if (\is_a($typeName, AbstractValue::class, \true)
						|| \is_a($typeName, Context::class, \true)
					) {
						$types[] = $typeName;
					} else {
						$invalid = "Type '$typeName' is not an allowed type";
					}

				}

			}

			if ($invalid) {

				$declaringClass = $rp->getDeclaringClass();
				$className = $declaringClass
					? $declaringClass->getName()
					: \null;

				$fnName = $rp->getDeclaringFunction()->getName();
				$paramName = $rp->getName();
				$paramPosition = $rp->getPosition();
				$fqn = $className ? "{$className}::{$fnName}()" : "{$fnName}()";

				$msg = "Parameter {$paramPosition} '\${$paramName}' for function {$fqn} "
					. "is invalid: $invalid";

				throw new EngineError($msg);

			};

		}

		return $types;

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
	 */
	public static function yield_left_to_right(array $node, Context $ctx) {

		foreach ($node['operands'] as $i => $operand) {

			// First operator will be null and the last one too.
			$operator = $node['ops'][$i - 1]['text'] ?? \null;
			$value = HandlerFactory::runNode($operand, $ctx);

			yield [$operator, $value];

		}

	}

	/**
	 * Converts PHP class names to Primi type names represented as string.
	 * Types can be passed as a single class name or array of PHP class names.
	 *
	 * Throws an exception if any PHP class name doesn't represent a Primi type.
	 */
	public static function php_types_to_primi_types($types): string {

		$types = \is_string($types) ? [$types] : $types;
		$primiTypes = \array_map(function($class) {

			// Resolve PHP nulls as Primi nulls.
			if ($class === 'null') {
				return 'null';
			}

			if (!\is_a($class, AbstractValue::class, \true)) {
				throw new EngineInternalError(
					"Cannot convert PHP class name '$class' to Primi type"
				);
			}

			return $class->getTypeName();

		}, $types);

		return \implode('|', $primiTypes);

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
	 * Lookup and return attr from a type object - or its parents.
	 *
	 * If the `$bind` argument is specified and the attr is a function, this
	 * returns a new `FuncValue` with partial arguments having the object
	 * in the `$bind` argument (this is how Primi handles object methods -
	 * the instance object is bound as the first argument of the function its
	 * type object provides).
	 *
	 * @return AbstractValue|null
	 */
	public static function attr_lookup_type_hierarchy(
		TypeValue $typeObject,
		string $attrName,
		?AbstractValue $bind = \null
	) {

		//
		// Try attr access on the type object itself.
		//
		// Example - Accessing `SomeClass.some_attr`:
		// Try if there's `some_attr` attribute in the SomeClass type itself.
		//

		if ($value = $typeObject->rawAttrGet($attrName)) {
			if ($bind && $value instanceof FuncValue) {
				$args = new CallArgs([$bind]);
				return new FuncValue($value->getInternalValue(), $args);
			}
			return $value;
		}

		//
		// If the type object itself doesn't have this attr, try inheritance -
		// look for the attr in the parent type objects.
		//

		while ($typeObject = $typeObject->getParentType()) {
			if ($value = $typeObject->rawAttrGet($attrName)) {
				if ($bind && $value instanceof FuncValue) {
					$args = new CallArgs([$bind]);
					return new FuncValue($value->getInternalValue(), $args);
				}
				return $value;
			}
		}

		return \null;

	}

}
