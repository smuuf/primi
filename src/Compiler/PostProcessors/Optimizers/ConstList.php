<?php

declare(strict_types=1);

namespace Smuuf\Primi\Compiler\PostProcessors\Optimizers;

use Smuuf\Primi\Compiler\BytecodeDLL;
use Smuuf\Primi\Compiler\Op;
use Smuuf\Primi\Compiler\PostProcessors\Peephole\PeepholePattern;
use Smuuf\Primi\VM\Machine;

/**
 * Constant list optimizer.
 *
 * Converts:
 *
 * ```
 * OP_LOAD_CONST 123
 * OP_LOAD_CONST "a"
 * OP_LOAD_CONST "bcd"
 * OP_LOAD_CONST 456
 * OP_BUILD_LIST, 4
 * ```
 *
 * Into:
 *
 * ```
 * OP_BUILD_LIST, [123, "a", "bcd", 456]
 * ```
 */
class ConstList implements OptimizerInterface {

	use \Smuuf\StrictObject;

	public function optimize(BytecodeDLL $bytecode): bool {

		$pattern = (new PeepholePattern)
			->add(
				opType: Machine::OP_LOAD_CONST,
				// Increase the counter of OP_LOAD_CONST ops encountered in a row.
				onMatch: fn(Op $op, array &$storage) => $storage['count'] = ($storage['count'] ?? 0) + 1,
				many: true,
			)
			->add(
				opType: Machine::OP_BUILD_LIST,
				// Filter out matching opTypes that don't use the same
				// number of arguments as we counted ops in previous rule.
				filter: fn(Op $op, array &$storage) => $storage['count'] === $op->args[0],
			);

		$changed = false;
		foreach ($pattern->scan($bytecode) as $replacer) {

			$replacer(function(array $match) use (&$changed): array {

				// Remove the final OP_BUILD_LIST op.
				array_pop($match);

				/** @var array<Op> $match */
				$result = [
					new Op(
						opType: Machine::OP_BUILD_CONST_LIST,
						args: [array_map(fn($op) => $op->args[0], $match)],
					),
				];
				$changed = true;

				// Return list of replacement opcodes.
				return $result;

			});

		}

		return $changed;

	}

}
