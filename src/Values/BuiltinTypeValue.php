<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

/**
 * Sometimes Primi engine needs to differentiate between:
 * 1) Builtin global and immutable Primi types are at the core of Primi
 * engine.
 * 2) All other Primi types (either implemented in PHP or created in Primi
 * userland).
 *
 * Therefore all Primi type objects that represent the most basic builtin types
 * as mentioned 1) will be represented as inctances of this PHP class (instead
 * of just PHP `TypeValue` class).
 *
 * @see \Smuuf\Primi\Stdlib\BuiltinTypes
 * @internal
 */
final class BuiltinTypeValue extends TypeValue {

	public function getStringRepr(): string {
		return "<builtin type '{$this->name}'>";
	}

}
