<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Handlers\ChainedHandler;

class AttrAccess extends ChainedHandler {

	public static function chain(
		array $node,
		Context $context,
		AbstractValue $subject
	) {

		$attrName = StringValue::build($node['attr']);

		//
		// If the UFCS didn't match any typed call, try ordinary attr access.
		//

		//$value = $subject->type->attrGet($attrName);

		//
		// If the UFCS didn't match any typed call, try ordinary attr access.
		//

		$value = $subject->attrGet($attrName);

		if ($value) {
			return $value;
		}

		throw new RuntimeError(\sprintf(
			"Type '%s' does not support attribute access", $subject::TYPE
		));

	}

	private static function inferTypedName(
		string $name,
		AbstractValue $v
	): string {
		return \sprintf("%s_%s", $v::TYPE, $name);
	}

	public static function reduce(array &$node): void {
		$node['attr'] = $node['attr']['text'];
	}

}
