<?php

declare(strict_types=1);

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Helpers\Interned;
use Smuuf\Primi\Handlers\Handler;
use Smuuf\Primi\Compiler\Compiler;

class NumberLiteral extends Handler {

	public static function reduce(array &$node): void {

		// As string.
		$node['number'] = \str_replace('_', '', $node['text']);
		unset($node['text']);

	}

	public static function compile(Compiler $bc, array $node): void {
		$bc->add(Machine::OP_LOAD_CONST, Interned::number($node['number']));
	}

}
