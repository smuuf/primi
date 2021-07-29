<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Handlers\ChainedHandler;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Handlers\Types\Variable;

class AttrAccess extends ChainedHandler {

	public static function chain(
		array $node,
		Context $context,
		AbstractValue $subject
	) {

		$attrName = StringValue::build($node['core']['attr']);

		//
		// If the UFCS didn't match any typed call, try ordinary attr access.
		//

		//$value = $subject->type->attrGet($attrName);

		//
		// If the UFCS didn't match any typed call, try ordinary attr access.
		//

		$typedName = self::inferTypedName($attrName->getInternalValue(), $subject);

		$fn = $context->getVariable($typedName);

		if ($fn === null) {
			$fn = Variable::fetch($attrName->getInternalValue(), $context);
		}

		$invocation = $node['chain']['core'];
		$invocation['prepend_arg'] = $subject;

		$value = HandlerFactory::getFor('Invocation')::chain($invocation, $context, $fn);
		
		$fn = function() use($value){return $value;};
		$value = AbstractValue::buildAuto($fn);
		/**/
		
		if ($value) {
			return $value;
		}

		throw new RuntimeError(\sprintf(
			"Type '%s' does not support attribute access", $subject::TYPE
		));

	}

	private static function inferTypedName(
		string $name,
		AbstractValue $v
	): string {
		return \sprintf("%s_%s", $v::TYPE, $name);
	}

	public static function reduce(array &$node): void {
		$node['attr'] = $node['attr']['text'];
	}

}
