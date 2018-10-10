<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Handlers\Variable;
use \Smuuf\Primi\UndefinedVariableException;

class ChainedFunction extends \Smuuf\Primi\StrictObject implements IChainedHandler {

	public static function chain(
		array $node,
		Context $context,
		Value $subject
	) {

		// 1) We'll extract the function name directly from the "fn" child node.
		$name = $node['fn']['text'];

		// 2) We'll try to find type-matching function first.
		// That is: A function which name is prepended by the subject's type
		// name. For example, if function name is "pop" and subject's type is
		// "array", we'll try to find "array_pop" function first.
		// If no function (variable) matching the type of the subject is found,
		// only after then we'll try to fetch the function with the originally
		// specified name.
		$typedName = self::inferTypedName($name, $subject);

		try {
			$fn = Variable::fetch($typedName, $node, $context);
		} catch (UndefinedVariableException $e) {
			$fn = Variable::fetch($name, $node, $context);
		}

		// Modify the invocation node to contain the subject. It's handler will
		// know what to do. Not a particulary pretty solution, so if you manage
		// to come up with something better, do it.
		$invocation = $node['invo'];
		$invocation['prepend_arg'] = $subject;

		return HandlerFactory
			::get($node['invo']['name'])
			::chain($invocation, $context, $fn);

	}

	private static function inferTypedName(string $name, Value $v): string {
		return sprintf("%s_%s", $v::TYPE, $name);
	}

}
