<?php

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use \Smuuf\Primi\Extensions\PrimiFunc;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Values\TypeValue;
use \Smuuf\Primi\Values\NullValue;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Extensions\TypeExtension;
use \Smuuf\Primi\Stdlib\StaticTypes;

class NullTypeExtension extends TypeExtension {

	#[PrimiFunc]
	public static function __new__(TypeValue $type): NullValue {

		if ($type !== StaticTypes::getNullType()) {
			throw new TypeError("Passed invalid type object");
		}

		return Interned::null();

	}

}
