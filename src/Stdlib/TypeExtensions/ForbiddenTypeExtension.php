<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use Smuuf\Primi\Values\TypeValue;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\Extensions\PrimiFunc;
use Smuuf\Primi\Extensions\TypeExtension;

class ForbiddenTypeExtension extends TypeExtension {

	#[PrimiFunc]
	public static function __new__(
		TypeValue $type,
		?AbstractValue $value = \null,
	): never {

		Exceptions::piggyback(
			StaticExceptionTypes::getRuntimeErrorType(),
			"Object of type '{$type->getName()}' cannot be created directly",
		);

	}

}
