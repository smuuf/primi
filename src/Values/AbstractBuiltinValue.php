<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

/**
 * Base PHP class for representing all Primi values/object builtin into the
 * engine.
 */
abstract class AbstractBuiltinValue extends AbstractValue {

	public function getTypeName(): string {
		return static::TYPE;
	}

}
