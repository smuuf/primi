<?php

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\LookupError;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Handlers\ChainedHandler;

class AttrAccess extends ChainedHandler {

	public static function chain(
		array $node,
		Context $context,
		AbstractValue $subject
	) {

		$attrName = $node['attr'];
		$value = $subject->attrGet($attrName);

		if ($value) {
			return $value;
		}

		$typeName = $subject->getTypeName();
		throw new LookupError("Object of type '$typeName' has no attribute '$attrName'");

	}

	public static function reduce(array &$node): void {
		$node['attr'] = $node['attr']['text'];
	}

}
