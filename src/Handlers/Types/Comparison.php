<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Helpers\ComparisonLTR;

class Comparison extends SimpleHandler {

	protected static function handle(array $node, Context $context) {
		return ComparisonLTR::handle($node, $context);
	}

	public static function reduce(array &$node): void {

		// If there is no operator, that means there's only one operand.
		// In that case, return only the operand node inside.
		if (!isset($node['ops'])) {
			$node = $node['operands'];
		} else {
			$node['ops'] = Func::ensure_indexed($node['ops']);
		}

	}

}
