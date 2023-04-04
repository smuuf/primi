<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;

class Negation extends Handler {

	public static function reduce(array &$node): void {

		// If this truly has a negation, do not reduce this node.
		// If not, return only core.
		if (!isset($node['nots'])) {
			$node = $node['core'];
		} else {
			$node['nots'] = Func::ensure_indexed($node['nots']);
		}

	}

	public static function compile(Compiler $bc, array $node): void {

		$bc->inject($node['core']);

		$isNegation = \count($node['nots'] ?? []) % 2;
		if ($isNegation) {
			$bc->add(Machine::OP_NEGATE);
		} else {
			$bc->add(Machine::OP_CAST_BOOL);
		}

	}

}
