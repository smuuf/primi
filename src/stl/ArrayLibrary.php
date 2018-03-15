<?php

namespace Smuuf\Primi\Stl;

use \Smuuf\Primi\Library;
use \Smuuf\Primi\Structures\ArrayValue;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\ErrorException;

abstract class ArrayLibrary extends Library {

	public static function contains(ArrayValue $self, Value $value) {

		// Let's search the $value object in $self's value (array of objects).
		return new BoolValue(\array_search($value, $self->value) !== \false);

	}

	public static function push(ArrayValue $self, Value $value) {
		$self->value[] = $value;
		return $value;
	}

	public static function pop(ArrayValue $self) {
		return \array_pop($self->value);
	}

}
