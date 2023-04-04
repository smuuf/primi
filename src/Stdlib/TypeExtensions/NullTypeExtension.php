<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use Smuuf\Primi\Values\TypeValue;
use Smuuf\Primi\Values\NullValue;
use Smuuf\Primi\Stdlib\StaticTypes;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\Helpers\Interned;
use Smuuf\Primi\Extensions\PrimiFunc;
use Smuuf\Primi\Extensions\TypeExtension;

class NullTypeExtension extends TypeExtension {

	#[PrimiFunc]
	public static function __new__(TypeValue $type): NullValue {

		if ($type !== StaticTypes::getNullType()) {
			Exceptions::piggyback(
				StaticExceptionTypes::getTypeErrorType(),
				"Passed invalid type object",
			);
		}

		return Interned::null();

	}

}
