<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Compiler\MetaFlag;
use Smuuf\Primi\Handlers\Handler;

class Invocation extends Handler {

	public static function compile(Compiler $bc, array $node): void {

		// Special case for calling without arguments.
		if (empty($node['argList'])) {
			$bc->add(Machine::OP_CALL_FUNCTION_N);
			return;
		}

		$bc->withMetaFrame(function() use ($bc, $node) {

			$bc->inject($node['argList']);

			$isComplex = $bc->getMeta(
				MetaFlag::ComplexArgs,
				false,
			);

			if ($isComplex) {
				$bc->add(Machine::OP_CALL_FUNCTION_EX);
			} else {
				$argCount = count($node['argList']['args']);
				$bc->add(Machine::OP_CALL_FUNCTION, $argCount);
			}

		});

	}

}
