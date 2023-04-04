<?php

declare(strict_types=1);

namespace Smuuf\Primi\Compiler\PostProcessors\Optimizers;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Compiler\Op;
use Smuuf\Primi\Compiler\BytecodeDLL;
use Smuuf\Primi\Compiler\PostProcessors\Peephole\PeepholePattern;

/**
 * Reduces an unnecessary load after storing a name.
 *
 * ```
 * (some value is on stack)
 * 1 OP_STORE_NAME 'whatever'
 * 2 OP_LOAD_NAME  'whatever'
 * (the same value is on the stack - same name was used)
 * ```
 *
 * into:
 *
 * ```
 * (some value is on stack)
 * 1 OP_DUP_TOP
 * 2 OP_STORE_NAME 'whatever'
 * (the same value is on the stack)
 * ```
 *
 * The stack's top item duplication is much simpler operation than loading a
 * variable from the scope.
 */
class UselessStoreLoad implements OptimizerInterface {

	use \Smuuf\StrictObject;

	public function optimize(BytecodeDLL $bytecode): bool {

		$pattern = (new PeepholePattern)
			->add(
				opType: Machine::OP_STORE_NAME,
				// Store the name for next rule.
				onMatch: fn(Op $op, array &$storage) => $storage['name'] = $op->args[0],
			)
			->add(
				opType: Machine::OP_LOAD_NAME,
				// Filter out matching ops that don't use the same
				// name as in previous rule.
				filter: fn(Op $op, array &$storage) => $storage['name'] === $op->args[0],
			);

		$changed = false;
		foreach ($pattern->scan($bytecode) as $replacer) {

			$replacer(function(array $match) use (&$changed): array {

				/** @var array<Op> $match */
				$result = [
					new Op(opType: Machine::OP_DUP_TOP),
					$match[0], // Original OP_STORE_NAME.
				];
				$changed = true;

				// Return list of replacement opcodes.
				return $result;

			});

		}

		return $changed;

	}

}
