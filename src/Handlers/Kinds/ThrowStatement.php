<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;

class ThrowStatement extends Handler {

	/**
	 * @phpstan-param TypeDef_AstNode $node
	 */
	public static function compile(Compiler $bc, array $node): void {
		$bc->inject($node['exc']);
		$bc->add(Machine::OP_EXC_THROW);
	}

}
