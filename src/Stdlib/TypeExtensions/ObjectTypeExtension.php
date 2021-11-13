<?php

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\InstanceValue;
use \Smuuf\Primi\Values\TypeValue;
use \Smuuf\Primi\Extensions\TypeExtension;
use \Smuuf\Primi\Structures\CallArgs;

class ObjectTypeExtension extends TypeExtension {

	/**
	 * @primi.function(inject-context, no-stack)
	 */
	public static function __new__(
		Context $ctx,
		TypeValue $type,
		AbstractValue ...$args
	): AbstractValue {

		$object = new InstanceValue($type);
		if ($init = $object->attrGet('__init__')) {
			$init->invoke($ctx, new CallArgs($args));
		}

		return $object;

	}

}
