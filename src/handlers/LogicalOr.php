<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\InternalUndefinedTruthnessException;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\LogicalLTR;

class LogicalOr extends \Smuuf\Primi\StrictObject implements IHandler, IReducer {

	public static function handle(array $node, Context $context) {

		try {
			return LogicalLTR::handle($node, $context);
		} catch (InternalUndefinedTruthnessException $e) {
			throw new ErrorException($e->getMessage(), $node);
		}

	}

	public static function reduce(array $node) {

		if (!isset($node['ops'])) {
			return $node['operands'];
		}

	}

}
