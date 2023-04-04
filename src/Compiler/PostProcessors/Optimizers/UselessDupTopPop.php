<?php

declare(strict_types=1);

namespace Smuuf\Primi\Compiler\PostProcessors\Optimizers;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Compiler\BytecodeDLL;
use Smuuf\Primi\Compiler\PostProcessors\Peephole\PeepholePattern;

class UselessDupTopPop implements OptimizerInterface {

	use \Smuuf\StrictObject;

	private const PUSHING_OPS = [
		Machine::OP_STORE_NAME,
		Machine::OP_STORE_ITEM,
		Machine::OP_STORE_ATTR,
		Machine::OP_CALL_FUNCTION,
		Machine::OP_CALL_FUNCTION_N,
	];

	public function optimize(BytecodeDLL $bytecode): bool {

		$pattern = (new PeepholePattern)
			->add(Machine::OP_DUP_TOP)
			->add(self::PUSHING_OPS)
			->add(Machine::OP_POP);

		$changed = false;
		foreach ($pattern->scan($bytecode) as $replacer) {

			$replacer(function(array $match) use (&$changed): array {

				// Reduced to single op.
				$result = $match[1];
				$changed = true;

				// Return list of replacement opcodes.
				return [$result];

			});

		}

		return $changed;

	}

}
