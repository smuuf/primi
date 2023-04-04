<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\Handlers\Handler;
use Smuuf\Primi\Compiler\Compiler;

class AttrAccess extends Handler {

	public static function fetch(AbstractValue $subject, string $name): AbstractValue {

		$value = $subject->attrGet($name);
		if ($value === \null) {
			Exceptions::piggyback(
				StaticExceptionTypes::getAttributeErrorType(),
				"Object of type '{$subject->getTypeName()}' has no attribute '$name'",
			);
		}

		return $value;

	}

	public static function store(
		AbstractValue $subject,
		string $name,
		AbstractValue $value,
	): void {

		$success = $subject->attrSet($name, $value);
		if ($success === \false) {
			Exceptions::piggyback(
				StaticExceptionTypes::getAttributeErrorType(),
				"Object of type '{$subject->getTypeName()}' does not support attribute assignment",
			);
		}

	}

	public static function reduce(array &$node): void {
		$node['attr'] = $node['attr']['text'];
	}

	public static function compile(Compiler $bc, array $node): void {
		$bc->add(Machine::OP_LOAD_ATTR, $node['attr']);
	}

}
