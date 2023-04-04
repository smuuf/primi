<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\TypeExtensions\Exc;

use Smuuf\Primi\Context;
use Smuuf\Primi\Ex\TypeError;
use Smuuf\Primi\Values\ExceptionValue;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Helpers\Types;
use Smuuf\Primi\Extensions\PrimiFunc;
use Smuuf\Primi\Extensions\TypeExtension;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\Structures\CallArgs;

class BaseExceptionTypeExtension extends TypeExtension {

	#[PrimiFunc(callConv: PrimiFunc::CONV_CALLARGS)]
	public static function __new__(
		CallArgs $args,
		Context $ctx,
	): AbstractValue {

		$posArgs = $args->getArgs();
		$type = \array_shift($posArgs);

		if (!Types::isSubtypeOf($type, StaticExceptionTypes::getBaseExceptionType())) {
			Exceptions::piggyback(
				StaticExceptionTypes::getTypeErrorType(),
				"Passed invalid type object",
			);
		}

		if ($args->getKwargs()) {
			/** @var TypeValue $type */
			$typeName = $type->getName();
			Exceptions::piggyback(
				StaticExceptionTypes::getTypeErrorType(),
				"$typeName.__new__() takes no keyword args",
			);
		}

		return new ExceptionValue($type, $ctx, $posArgs);

	}

}
