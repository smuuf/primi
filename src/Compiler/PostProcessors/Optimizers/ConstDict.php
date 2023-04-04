<?php

declare(strict_types=1);

namespace Smuuf\Primi\Compiler\PostProcessors\Optimizers;

use Smuuf\Primi\Compiler\BytecodeDLL;
use Smuuf\Primi\Compiler\Op;
use Smuuf\Primi\Compiler\PostProcessors\Peephole\PeepholePattern;
use Smuuf\Primi\Ex\EngineInternalError;
use Smuuf\Primi\VM\Machine;

/**
 * Constant dict optimizer.
 *
 * Converts:
 *
 * ```
 * OP_LOAD_CONST 123
 * OP_LOAD_CONST "a"
 * OP_LOAD_CONST "bcd"
 * OP_LOAD_CONST 456
 * OP_BUILD_DICT, 2
 * ```
 *
 * Into:
 *
 * ```
 * OP_BUILD_DICT, [123, "a", "bcd", 456]
 * ```
 */
class ConstDict implements OptimizerInterface {

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
				opType: Machine::OP_BUILD_DICT,
				// Filter out matching opTypes that don't use the same
				// number of arguments as we counted ops in previous rule.
				filter: fn(Op $op, array &$storage) => $storage['count'] === ($op->args[0] * 2),
			);

		$changed = false;
		foreach ($pattern->scan($bytecode) as $replacer) {

			$replacer(function(array $match) use (&$changed): array {

				// Remove the final OP_BUILD_LIST op.
				array_pop($match);

				if (count($match) % 2) {
					throw new EngineInternalError("Number of items is not even");
				}

				$consts = array_map(fn($op) => $op->args[0], $match);
				$pairs = array_chunk($consts, 2);

				/** @var array<Op> $match */
				$result = [
					new Op(
						opType: Machine::OP_BUILD_CONST_DICT,
						args: [$pairs],
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
