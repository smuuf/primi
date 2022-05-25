<?php

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use \Smuuf\Primi\Extensions\PrimiFunc;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Values\TypeValue;
use \Smuuf\Primi\Values\TupleValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Extensions\TypeExtension;

class ForbiddenTypeExtension extends TypeExtension {

	#[PrimiFunc]
	public static function __new__(
		TypeValue $type,
		?AbstractValue $value = \null
	): TupleValue {

		throw new RuntimeError(
			"Object of type '{$type->getName()}' cannot be created directly");

	}

}
