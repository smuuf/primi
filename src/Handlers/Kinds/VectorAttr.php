<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;

class VectorAttr extends Handler {

	public static function reduce(array &$node): void {
		$node['attr'] = $node['attr']['text'];
	}

	public static function compile(Compiler $bc, array $node): void {
		$bc->add(Machine::OP_LOAD_ATTR, $node['attr']);
	}

}
