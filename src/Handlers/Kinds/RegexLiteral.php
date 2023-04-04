<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Helpers\Interned;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;

class RegexLiteral extends Handler {

	public static function compile(Compiler $bc, array $node): void {

		// The core node's text is already prepared by StringLiteral - it is
		// already stripped of the quotes around the literal, so we can use it
		// directly.
		$const = Interned::regex($node['core']['text']);
		$bc->add(Machine::OP_LOAD_CONST, $const);

	}

}
