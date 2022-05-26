<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use \Smuuf\Primi\Extensions\PrimiFunc;
use \Smuuf\Primi\Values\TypeValue;
use \Smuuf\Primi\Values\DictValue;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Extensions\TypeExtension;

/**
 * This extension defines methods on the "type" type object - how the instance
 * of the type "type" behaves.
 */
class TypeTypeExtension extends TypeExtension {

	#[PrimiFunc]
	public static function __call__(
		AbstractValue $value,
		?TypeValue $parentType = \null,
		?DictValue $attrs = \null
	): TypeValue {

		// If more than one argument was provided, let's create a new type!
		if ($parentType === \null) {
			return $value->getType();
		}

		$attrs = $attrs
			? Func::couples_to_variables_array(
				Func::mapping_to_couples($attrs),
				'Attribute name'
			)
			: [];

		Func::allow_argument_types(1, $value, StringValue::class);
		return new TypeValue(
			$value->getStringValue(),
			$parentType,
			$attrs,
			\false
		);

	}

}
