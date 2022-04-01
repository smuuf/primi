<?php

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\InstanceValue;
use \Smuuf\Primi\Extensions\TypeExtension;
use \Smuuf\Primi\Structures\CallArgs;

class ObjectTypeExtension extends TypeExtension {

	/**
	 * @primi.func(no-stack, call-conv: callargs)
	 */
	public static function __new__(
		CallArgs $args,
		Context $ctx
	): AbstractValue {

		$posArgs = $args->getArgs();
		$type = \array_shift($posArgs);
		$callArgs = new CallArgs($posArgs, $args->getKwargs());

		$object = new InstanceValue($type, $ctx);
		if ($init = $object->attrGet('__init__')) {
			$init->invoke($ctx, $callArgs);
		}

		return $object;

	}

}
