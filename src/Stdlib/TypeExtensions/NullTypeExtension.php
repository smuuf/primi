<?php

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use \Smuuf\Primi\Values\TypeValue;
use \Smuuf\Primi\Values\NullValue;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Extensions\TypeExtension;

class NullTypeExtension extends TypeExtension {

	/**
	 * @primi.function(no-stack)
	 */
	public static function __new__(TypeValue $_): NullValue {
		return Interned::null();
	}

}
