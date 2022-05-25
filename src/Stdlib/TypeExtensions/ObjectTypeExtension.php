<?php

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Extensions\PrimiFunc;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Values\TypeValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\InstanceValue;
use \Smuuf\Primi\Extensions\TypeExtension;
use \Smuuf\Primi\Structures\CallArgs;

class ObjectTypeExtension extends TypeExtension {

	#[PrimiFunc(toStack: \true, callConv: PrimiFunc::CONV_CALLARGS)]
	public static function __new__(
		CallArgs $args,
		Context $ctx
	): AbstractValue {

		$posArgs = $args->getArgs();
		$type = \array_shift($posArgs);
		$callArgs = new CallArgs($posArgs, $args->getKwargs());

		if (!$type instanceof TypeValue) {
			throw new TypeError("First argument for object.__new__() must be a type");
		}

		$object = new InstanceValue($type, $ctx);
		if ($init = $object->attrGet('__init__')) {
			$init->invoke($ctx, $callArgs);
		}

		return $object;

	}

}
