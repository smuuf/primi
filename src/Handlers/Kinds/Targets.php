<?php

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Structures\AssignmentTargets;

class Targets extends SimpleHandler {

	protected static function handle(array $node, Context $context) {
		return new AssignmentTargets($node['t']);
	}

	public static function reduce(array &$node): void {

		// Make sure this is always list, even with one item.
		$node['t'] = \array_column(Func::ensure_indexed($node['t']), 'text');

	}

}
