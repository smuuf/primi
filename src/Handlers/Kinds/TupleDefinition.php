<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;

class TupleDefinition extends Handler {

	public static function reduce(array &$node): void {

		// Make sure this is always list, even with one item.
		if (isset($node['items'])) {
			$node['items'] = Func::ensure_indexed($node['items']);
		}

	}

	public static function compile(Compiler $bc, array $node) {

		$itemNodes = ($node['items'] ?? []);
		foreach ($itemNodes as $itemNode) {
			$bc->inject($itemNode);
		}

		$bc->add(Machine::OP_BUILD_TUPLE, count($itemNodes));

	}

}
