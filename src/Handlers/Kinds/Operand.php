<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;

class Operand extends Handler {

	public static function reduce(array &$node): void {

		// If this node has a value method call with it, don't reduce it.
		if (!isset($node['chain'])) {
			$node = $node['core'];
		}

	}

	public static function compile(Compiler $bc, array $node): void {

		$bc->inject($node['core']);

		if (isset($node['chain'])) {
			$bc->inject($node['chain']);
		}

	}

}
