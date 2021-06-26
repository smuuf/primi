<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Handlers\ChainedHandler;
use \Smuuf\Primi\Structures\AttrInsertionProxy;

class VectorAttr extends ChainedHandler {

	public static function chain(
		array $node,
		Context $context,
		AbstractValue $subject
	) {

		$attrName = StringValue::build($node['attr']);

		// If this is a leaf node, return an insertion proxy instead of value.
		if ($node['leaf']) {
			return new AttrInsertionProxy($attrName, $subject);
		}

		$value = $subject->attrGet($attrName);
		if ($value === \null) {
			throw new RuntimeError(\sprintf(
				"Type '%s' does not support attribute access", $subject::TYPE
			));
		}

		return $value;

	}

	public static function reduce(array &$node): void {
		$node['attr'] = $node['attr']['text'];
	}

}
