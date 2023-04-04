<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;

class Exponentiation extends Handler {

	public static function reduce(array &$node): void {

		// If there is no factor, then there's no need to keep this as
		// a complex node of this type. Reduce this node to its only operand.
		if (!isset($node['factor'])) {
			$node = $node['operand'];
		}

	}

	public static function compile(Compiler $bc, array $node): void {

		$bc->inject($node['operand']);
		$bc->inject($node['factor']);
		$bc->add(Machine::OP_EXP);

	}

}
