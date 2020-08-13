<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Helpers\ComparisonLTR;

use function \Smuuf\Primi\Helpers\ensure_indexed as primifn_ensure_indexed;

class Comparison extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		try {
			return ComparisonLTR::handle($node, $context);
		} catch (RuntimeError $e) {
			throw new RuntimeError($e->getMessage(), $node);
		}

	}

	public static function reduce(array &$node): void {

		// If there is no operator, that means there's only one operand.
		// In that case, return only the operand node inside.
		if (!isset($node['ops'])) {
			$node = $node['operands'];
		} else {
			$node['ops'] = primifn_ensure_indexed($node['ops']);
		}

	}

}
