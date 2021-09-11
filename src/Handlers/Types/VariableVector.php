<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Handlers\SimpleHandler;
use Smuuf\Primi\Helpers\Func;

/**
 * This handler is used to access nested items or attributes and prepare it
 * for nested assignment.
 *
 * For example, this is a vector:
 * ```js
 * a[1].b['x'].c[0] = 'yes'
 * ```
 *
 * The code is saying that someone wants to:
 *
 * _Store 'yes' under index `0` of the value `c`, which is an attribute of value
 * stored in `b` under the key `x`, which itself is the value stored under
 * index `1` of the value `a`._
 *
 * We need to process this chunk of AST nodes and **return an insertion proxy**,
 * which can then be used for assignment in the `Assignment` handler.
 */
class VariableVector extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		// Retrieve the original value.
		$value = HandlerFactory::runNode($node['core'], $context);

		// And handle the nesting according to the specified vector.
		foreach ($node['vector'] as $next) {
			$handler = HandlerFactory::getFor($next['name']);
			$value = $handler::chain($next, $context, $value);
		}

		return $value;

	}

	public static function reduce(array &$node): void {

		$node['vector'] = Func::ensure_indexed($node['vector']);

		// Mark the last vector node as leaf, so it knows that we expect
		// insertion proxy from it.
		$first = true;
		for ($i = count($node['vector']); $i !== 0; $i--) {
			$node['vector'][$i - 1]['leaf'] = $first;
			$first = false;
		}

	}

}
