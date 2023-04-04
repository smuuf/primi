<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Handlers\Handler;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;

class Dereference extends Handler {

	public static function fetch(
		AbstractValue $subject,
		AbstractValue $key,
	): AbstractValue {

		$value = $subject->itemGet($key);
		if ($value === \null) {
			Exceptions::piggyback(
				StaticExceptionTypes::getTypeErrorType(),
				"Object of type '{$subject->getTypeName()}' does not support item access",
			);
		}

		return $value;

	}

	public static function store(
		AbstractValue $subject,
		?AbstractValue $key,
		AbstractValue $value,
	): void {

		$success = $subject->itemSet($key, $value);
		if ($success === \false) {
			Exceptions::piggyback(
				StaticExceptionTypes::getTypeErrorType(),
				"Object of type '{$subject->getTypeName()}' does not support item access",
			);
		}

	}

	public static function compile(Compiler $bc, array $node) {
		$bc->inject($node['key']);
		$bc->add(Machine::OP_LOAD_ITEM);
	}

}
