<?php

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Structures\AssignmentTargets;

class Targets extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		$targetNames = \array_column($node['t'], 'text');
		return new AssignmentTargets($targetNames);

	}

	public static function reduce(array &$node): void {

		// Make sure this is always list, even with one item.
		if (isset($node['t'])) {
			$node['t'] = Func::ensure_indexed($node['t']);
		}

	}

}
