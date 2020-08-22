<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Structures\Value;

abstract class Func {

	/**
	 * Returns false if the passed array has contignuous numeric keys starting
	 * from 0 (i.e. it is a "list"). Returns true otherwise (i.e. it is a
	 * "dictionary).
	 *
	 * The solution was chosen based on 'array_list_dict' phpcb benchmark.
	 */
	public static function is_array_dict(array $input): bool {

		// Let's say that empty PHP array is not a dictionary.
		if (!$input) {
			return \false;
		}

		$c = 0;
		foreach ($input as $i => $_) {
			if ($c++ !== $i) {
				return \true;
			}
		}

		return \false;

	}

	public static function is_numeric_int(string $input): bool {
		// Solution based on 'numeric_int' phpcb benchmark results.
		return (bool) \preg_match("#^[+-]?\d+$#", $input);
	}

	public static function is_numeric(string $input): bool {
		return (bool) \preg_match('#^[+-]?\d+(\.\d+)?$#', $input);
	}

	public static function object_hash($o): string {
		return \substr(\md5(\spl_object_hash($o)), 0, 6);
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
	public static function is_any_of_types(?Value $value, string ...$types): bool {

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
	 * Helper for easy type-checking inside Primi extensions.
	 * Checks if a N-th parameter value is of a certain allowed type(s) and
	 * throws a TypeError if it's not.
	 * The exception is handled by Primi's function-invoking logic and converted
	 * into a user-readable error.
	 */
	public static function allow_argument_types(
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

		throw new TypeError(
			$arg::TYPE,
			$allowedTypes,
			"as argument $index"
		);

	}

	/**
	 * Takes array as reference and ensures its contents are represented in a form
	 * of indexed sub-arrays. This comes handy if we want to be sure that multiple
	 * AST sub-nodes (which PHP-PEG parser returns) are universally iterable.
	 */
	public static function ensure_indexed(array $array): array {
		return !isset($array[0]) ? [$array] : $array;
	}

	public static function hash(...$args): string {
		return \md5(\json_encode($args));
	}

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
	 * Parse \ArgumentCountError's message and return a tuple of integers
	 * representing:
	 * 1. Number of arguments passed.
	 * 2. Number of arguments expected.
	 */
	public static function parse_argument_count_error(\ArgumentCountError $e): array {

		$msg = $e->getMessage();

		// ArgumentCountError exception does not provide these numbers itself,
		// so we have to extract it from the internal PHP exception message.
		if (!preg_match('#(?<passed>\d+)\s+passed.*(?<expected>\d+)\s+expected#', $msg, $m)) {
			return [\null, \null];
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
	public static function get_primi_parameter_types_from_function(
		\ReflectionFunction $rf
	): array {

		$types = [];
		foreach ($rf->getParameters() as $rp) {

			$type = $rp->getType();

			// a) No typehint or b) typehint not hinting some Value class
			// means invalid type - gonna throw exception in that case.
			$invalidType = $type === \null
				|| !\is_a($type->getName(), Value::class, \true);

			if ($invalidType) {

				$declClass = $rp->getDeclaringClass();
				$class = $declClass
					? $declClass->getName()
					: \null;

				$method = $rp->getDeclaringFunction()->getName();
				$paramName = $rp->getName();
				$paramPosition = $rp->getPosition();
				$fqn = $class ? "{$class}::{$method}()" : "{$method}()";

				$msg = "Parameter '\${$paramName}' (#{$paramPosition}) of "
					. "{$fqn} doesn't have Primi value class typehint.";

				throw new RuntimeError($msg);

			};

			$types[] = $type->getName();

		}

		return $types;

	}

	public static function yield_left_to_right(array $node, Context $context) {

		$operands = $node['operands'];

		$firstOperand = $operands[0];
		$handler = HandlerFactory::get($firstOperand['name']);
		$first = $handler::handle($firstOperand, $context);

		yield $first;

		// Go through each of the operands and yield tuples of
		// [operand 1, operator, operand 2] that go after each other.
		$opCount = \count($operands);
		for ($i = 1; $i < $opCount; $i++) {

			$nextOperand = $operands[$i];
			$handler = HandlerFactory::get($nextOperand['name']);
			$next = $handler::handle($nextOperand, $context);

			// Extract the text of the assigned operator node.
			$op = $node['ops'][$i - 1]['text'];

			yield [$op, $next];

		}

	}

}
