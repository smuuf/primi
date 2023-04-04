<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;

class ThrowStatement extends Handler {

	public static function compile(Compiler $bc, array $node) {

		$bc->inject($node['exc']);
		$bc->add(Machine::OP_EXC_THROW);

	}

}
