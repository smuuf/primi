<?php

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\InternalPostProcessSyntaxError;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Structures\CallArgs;

/**
 * Node fields:
 * function: Function name.
 * args: List of arguments.
 * body: Node representing contents of code to execute as a function..
 */
class ArgumentList extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		if (!isset($node['args'])) {
			return [];
		}

		$args = [];
		$kwargs = [];

		foreach ($node['args'] as $arg) {
			if (isset($arg['argKey'])) {
				$key = HandlerFactory::runNode($arg['argKey'], $context);
				$kwargs[$key] = HandlerFactory::runNode($arg['argVal'], $context);
			} else {
				$args[] = HandlerFactory::runNode($arg['argVal'], $context);
			}
		}

		return new CallArgs($args, $kwargs);

	}

	public static function reduce(array &$node): void {

		// Make sure this is always list, even with one item.
		if (isset($node['args'])) {
			$node['args'] = Func::ensure_indexed($node['args']);
		}

		// Handle positional and keyword arguments.
		// If both types of arguments are used, keyword arguments MUST be
		// places after positional arguments. So let's check it.
		$foundKwargs = [];
		foreach ($node['args'] as $arg) {

			$isKwarg = isset($arg['argKey']);
			if (!$isKwarg && $foundKwargs) {

				// This is a positional argument, but we already encountered
				// some keyword argument - that's a syntax error (easier to
				// check and handle here and not via grammar).
				//
				// This happens if calling function like:
				// > result = f(1, arg_b: 2, 3)

				throw new InternalPostProcessSyntaxError(
					"Keyword arguments must be placed after positional arguments"
				);

			}

			if ($isKwarg) {

				$kwargKey = $arg['argKey']['text'];

				// Specifying a single kwarg multiple times is a syntax error.
				//
				// This happens if calling function like:
				// > f = (a, b, c) => {}
				// > result = f(1, b: 2, b: 3, c: 4)

				if (\array_key_exists($kwargKey, $foundKwargs)) {
					throw new InternalPostProcessSyntaxError(
						"Repeated keyword argument '$kwargKey'"
					);
				}

				// Monitor kwargs as keys in an array for faster lookup
				// via array_key_exists() above.
				$foundKwargs[$kwargKey] = \null;

			}

		}

	}

}
