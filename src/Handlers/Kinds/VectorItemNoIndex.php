<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;

class VectorItemNoIndex extends Handler {

	/**
	 * @phpstan-param TypeDef_AstNode $node
	 */
	public static function compile(Compiler $bc, array $node): void {
		$bc->add(Machine::OP_LOAD_ITEM, 1);
	}

}
