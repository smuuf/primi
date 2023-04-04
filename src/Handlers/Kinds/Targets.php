<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Compiler\MetaFlag;
use Smuuf\Primi\Handlers\Handler;

class Targets extends Handler {

	public static function reduce(array &$node): void {

		// Make sure this is always list, even with one item.
		$node['t'] = \array_column(Func::ensure_indexed($node['t']), 'text');

	}

	public static function compile(Compiler $bc, array $node) {

		$bc->setMeta(
			MetaFlag::TargetNames,
			$node['t'],
		);

	}

}
