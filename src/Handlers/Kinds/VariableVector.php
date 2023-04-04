<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Handlers\Handler;
use Smuuf\Primi\Compiler\Compiler;

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
class VariableVector extends Handler {

	public static function reduce(array &$node): void {

		$node['vector'] = Func::ensure_indexed($node['vector']);

		// Mark the last vector node as leaf, so it knows that we expect
		// insertion proxy from it.
		$first = \true;
		for ($i = \count($node['vector']); $i !== 0; $i--) {
			$node['vector'][$i - 1]['leaf'] = $first;
			$first = \false;
		}

	}

	public static function compile(Compiler $bc, array $node): void {

		$bc->inject($node['core']);

		// And handle the nesting according to the specified vector.
		foreach ($node['vector'] as $next) {
			$bc->inject($next);
		}

	}

}
