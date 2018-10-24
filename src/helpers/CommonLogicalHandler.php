<?php

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\InternalUndefinedTruthnessException;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\StrictObject;
use \Smuuf\Primi\Helpers\LogicalLTR;
use \Smuuf\Primi\Handlers\IHandler;
use \Smuuf\Primi\Handlers\IReducer;

/**
 * Common ancestor of LogicalAnd and LogicalOr handlers, both of which have
 * the exact same implementation, but are separated on a grammar level for
 * operators "and" and "or" to have a distinct precedences.
 */
abstract class CommonLogicalHandler extends StrictObject implements
	IHandler,
	IReducer
{

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
