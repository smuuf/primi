<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Values\TypeValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\InstanceValue;
use \Smuuf\Primi\Values\StaticTypeValue;
use \Smuuf\Primi\Extensions\PrimiFunc;
use \Smuuf\Primi\Extensions\TypeExtension;
use \Smuuf\Primi\Structures\CallArgs;

class ObjectTypeExtension extends TypeExtension {

	#[PrimiFunc(callConv: PrimiFunc::CONV_CALLARGS)]
	public static function __new__(
		CallArgs $args,
		Context $ctx,
	): AbstractValue {

		$posArgs = $args->getArgs();
		$type = \array_shift($posArgs);

		if (!$type instanceof TypeValue) {
			throw new TypeError("First argument for object.__new__() must be a type");
		}

		// Instantiated native types (non-userland) are represented as "real"
		// PHP objects (e.g. NumberValue, DictValue) and not PHP instances of
		// PHP class "InstanceValues". Therefore only userland types/classes
		// can be instantiated into PHP `InstanceValue` objects. Otherwise
		// things would get messy (for example `DictValue::__construct()` would
		// not get used in any way, etc.)
		if ($type instanceof StaticTypeValue) {
			throw new TypeError("First argument for object.__new__() must not be a native type");
		}

		return new InstanceValue($type, $ctx);

	}

}
