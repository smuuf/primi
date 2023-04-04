<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;

class Program extends Handler {

	public static function reduce(array &$node): void {

		// Make sure the list of statements has proper form.
		if (isset($node['stmts'])) {
			$node['stmts'] = Func::ensure_indexed($node['stmts']);
		} else {
			// ... even if there are no statements at all.
			$node['stmts'] = [];
		}

	}

	public static function compile(Compiler $bc, array $node): void {

		foreach ($node['stmts'] as $sub) {
			$bc->inject($sub);
		}

	}

}
