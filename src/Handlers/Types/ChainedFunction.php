<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Handlers\ChainedHandler;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Handlers\Types\Variable;

class ChainedFunction extends ChainedHandler {

	public static function chain(
		array $node,
		Context $context,
		AbstractValue $subject
	) {

		// 1) We'll extract the function name directly from the "fn" child node.
		$name = $node['fn']['var'];

		// 2) We'll try to find type-matching function first.
		// That is: A function which name is prepended by the subject's type
		// name. For example, if function name is "pop" and subject's type is
		// "array", we'll try to find "array_pop" function first.
		// If no function (variable) matching the type of the subject is found,
		// only after then we'll try to fetch the function with the originally
		// specified name.
		$typedName = self::inferTypedName($name, $subject);

		// Optimization: Get the inferred function directly, without risk of
		// exception being thrown - missing inferred function is not an error
		// and is quite expected, so don't complicate things with exceptions.
		$fn = $context->getVariable($typedName);
		if ($fn === \null) {
			$fn = Variable::fetch($name, $context);
		}

		// Modify the invocation node to contain the subject. Its handler will
		// know what to do. Not a particulary pretty solution, so if you manage
		// to come up with something better, do it.
		$invocation = $node['invo'];
		$invocation['prepend_arg'] = $subject;

		return HandlerFactory
			::getFor($node['invo']['name'])
			::chain($invocation, $context, $fn);

	}

	private static function inferTypedName(string $name, AbstractValue $v): string {
		return \sprintf("%s_%s", $v::TYPE, $name);
	}

}
