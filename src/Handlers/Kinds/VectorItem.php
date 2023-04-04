<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;

class VectorItem extends Handler {

	public static function compile(Compiler $bc, array $node): void {

		$bc->inject($node['index']);
		$bc->add(Machine::OP_LOAD_ITEM);

	}

}
