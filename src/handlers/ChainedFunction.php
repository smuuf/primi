<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\InternalUndefinedVariableException;
use \Smuuf\Primi\UndefinedVariableException;

class ChainedFunction extends \Smuuf\Primi\StrictObject implements IChainedHandler {

	public static function chain(
		array $node,
		Context $context,
		Value $subject
	) {

		$fn = HandlerFactory
			::get($node['fn']['name'])
			::handle($node['fn'], $context);

		// Modify the invocation node to contain the subject. It's handler will
		// know what to do. Not a particulary pretty solution, so if you manage
		// to come up with something better, do it.
		$invocation = $node['invo'];
		$invocation['prepend_arg'] = $subject;

		return HandlerFactory
			::get($node['invo']['name'])
			::chain($invocation, $context, $fn);

	}

}
