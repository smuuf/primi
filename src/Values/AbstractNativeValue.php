<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

/**
 * Base PHP class for representing all Primi values that are native to the
 * engine.
 */
abstract class AbstractNativeValue extends AbstractValue {

	public function getTypeName(): string {
		return static::TYPE;
	}

}
