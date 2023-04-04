<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use Smuuf\Primi\Context;
use Smuuf\Primi\Ex\TypeError;
use Smuuf\Primi\Values\TypeValue;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Values\InstanceValue;
use Smuuf\Primi\Values\StaticTypeValue;
use Smuuf\Primi\Stdlib\StaticTypes;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Helpers\Types;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\Structures\CallArgs;
use Smuuf\Primi\Extensions\PrimiFunc;
use Smuuf\Primi\Extensions\TypeExtension;

class ObjectTypeExtension extends TypeExtension {

	#[PrimiFunc(callConv: PrimiFunc::CONV_CALLARGS)]
	public static function __new__(
		CallArgs $args,
		Context $ctx,
	): AbstractValue {

		$argsList = $args->getArgs();
		$type = $argsList[0] ?? \null;

		if (!$type instanceof TypeValue) {
			Exceptions::piggyback(
				StaticExceptionTypes::getTypeErrorType(),
				"First argument for object.__new__() must be a type",
			);
		}

		// Instantiated native types (non-userland) are represented as "real"
		// PHP objects (e.g. NumberValue, DictValue) and not PHP instances of
		// PHP class "InstanceValue". Therefore only userland types/classes
		// can be instantiated into PHP `InstanceValue` objects. Otherwise
		// things would get messy (for example `DictValue::__construct()` would
		// not get used in any way, etc.)
		$static = $type;
		while ($static) {
			if ($static instanceof StaticTypeValue) {
				break;
			}
			$static = $static->getParentType();
		}

		if (!Types::isSameType(StaticTypes::getObjectType(), $static)) {
			$typeName = $type->getName();
			$staticTypeName = $static->getName();
			Exceptions::piggyback(
				StaticExceptionTypes::getTypeErrorType(),
				"Cannot call object.__new__($typeName). Use $staticTypeName.__new__($typeName)",
			);
		}

		return new InstanceValue($type, $ctx);

	}

}
