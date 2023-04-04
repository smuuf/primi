<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Ex\InternalSyntaxError;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Compiler\MetaFlag;
use Smuuf\Primi\Handlers\Handler;

/**
 * Node fields:
 * function: Function name.
 * args: List of arguments.
 * body: Node representing contents of code to execute as a function..
 */
class ArgumentList extends Handler {

	public static function reduce(array &$node): void {

		// Make sure this is always list, even with one item.
		if (isset($node['args'])) {
			$node['args'] = Func::ensure_indexed($node['args']);
		}

		// Handle positional and keyword arguments.
		// If both types of arguments are used, keyword arguments MUST be
		// placed after positional arguments. So check if that's the case.
		$foundKwargs = [];
		$foundAnyKwargs = \false;

		foreach ($node['args'] as $arg) {

			// Detect:
			// 1. literal keyword arguments, or
			// 2. Keyword arguments used as starred "**kwargs" argument.
			$isLiteralKwarg = isset($arg['argKey']);
			$areStarredKwargs =	$arg['name'] === 'StarredExpression'
				&& $arg['stars'] === StarredExpression::STARS_TWO;

			$isAnyKwarg = $isLiteralKwarg || $areStarredKwargs;

			if (!$isAnyKwarg && $foundAnyKwargs) {

				// This is a positional argument, but we already encountered
				// some keyword argument - that's a syntax error (easier to
				// check and handle here and not via grammar).
				//
				// This happens if calling function like:
				// > result = f(1, arg_b: 2, 3)

				throw InternalSyntaxError::fromNode(
					$node,
					"Keyword arguments must be placed after positional arguments"
				);

			}

			if ($isLiteralKwarg) {

				$kwargKey = $arg['argKey']['text'];

				// Specifying a single kwarg multiple times is a syntax error.
				//
				// This happens if calling function like:
				// > f = (a, b, c) => {}
				// > result = f(1, b: 2, b: 3, c: 4)

				if (\array_key_exists($kwargKey, $foundKwargs)) {
					throw InternalSyntaxError::fromNode(
						$node,
						"Repeated keyword argument '$kwargKey'"
					);
				}

				// Monitor kwargs as keys in an array for faster lookup
				// via array_key_exists() above.
				$foundKwargs[$kwargKey] = \null;

			}

			$foundAnyKwargs |= $isAnyKwarg;

		}

	}

	public static function compile(Compiler $bc, array $node): void {

		// Detect if this args list is "simple" or "complex".
		// Simple meaning that just variables or literals are used.
		// Complex meaning that keyword args or starred args are also used.

		$positionalListBuilt = false;
		$keywordDictBuilt = false;
		foreach ($node['args'] as $i => $argNode) {

			$starredArgType = $argNode['stars'] ?? StarredExpression::STARS_NONE;
			$isKwarg = isset($argNode['argKey']);
			$isComplexArg = $starredArgType || $isKwarg;

			if ($isComplexArg) {

				if (!$positionalListBuilt) {
					$bc->add(Machine::OP_BUILD_LIST, $i);
					$positionalListBuilt = true;
				}

				if (
					(
						$starredArgType === StarredExpression::STARS_TWO
						|| isset($argNode['argKey'])
					)
					&& !$keywordDictBuilt
				) {
					$bc->add(Machine::OP_BUILD_DICT, 0);
					$keywordDictBuilt = true;
				}

			}

			// Inject opcodes representing the argument itself.
			$bc->inject($argNode);

			if ($starredArgType) {

				if ($starredArgType === StarredExpression::STARS_ONE) {
					$bc->add(Machine::OP_LIST_EXTEND);
				}

				if ($starredArgType === StarredExpression::STARS_TWO) {
					$bc->add(Machine::OP_DICT_MERGE);
				}

			} elseif (
				isset($argNode['argKey'])
				&& $keywordDictBuilt
			) {
				$bc->add(Machine::OP_DICT_SET_ITEM, $argNode['argKey']['text']);
			} elseif ($positionalListBuilt) {
				$bc->add(Machine::OP_LIST_APPEND);
			}

		}

		// If in the end there's a positional list built, but no keyword dict,
		// create also an empty keyword dict, because the "complex function
		// call" expects both to be present.
		if ($positionalListBuilt && !$keywordDictBuilt) {
			$bc->add(Machine::OP_BUILD_DICT, 0);
		}

		// We'll tell our parent node's compile method if we used a simple or
		// complex args mode.
		$bc->setMeta(
			MetaFlag::ComplexArgs,
			$positionalListBuilt || $keywordDictBuilt,
		);

	}

}
