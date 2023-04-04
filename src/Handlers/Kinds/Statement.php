<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;

class Statement extends Handler {

	public static function compile(Compiler $bc, array $node): void {

		$handler = $bc->inject($node['core']);

		// Expressions leave a value on stack, so they can be consumed by
		// other outer expressions. Think "a = b = 1" where "b = 1" keeps
		// the 1 on the value stack so that "a" can also receive it.
		//
		// But if this is the most-outer expression (it's being injected as
		// statement-expression), we need to pop that value off the value
		// stack, because it's supposed to be discarded.
		//
		// Think "if (1) { a = 2; }" where the 2 should't leak out and should
		// be discarded..

		if ($handler === Expression::class) {
			$bc->add(Machine::OP_POP);
		}

	}

}
