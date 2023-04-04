<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;

class DictDefinition extends Handler {

	public static function reduce(array &$node): void {

		// Make sure this is always list, even with one item.
		if (isset($node['items'])) {
			$node['items'] = Func::ensure_indexed($node['items']);
		} else {
			$node['items'] = [];
		}

	}

	public static function compile(Compiler $bc, array $node) {

		foreach ($node['items'] as $sub) {
			$bc->inject($sub['key']);
			$bc->inject($sub['value']);
		}

		$bc->add(Machine::OP_BUILD_DICT, count($node['items']));

	}

}
