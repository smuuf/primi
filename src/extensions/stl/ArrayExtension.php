<?php

namespace Smuuf\Primi\Stl;

use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Structures\ArrayValue;
use \Smuuf\Primi\Structures\FuncValue;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\ErrorException;

abstract class ArrayExtension extends Extension {

	public static function copy(ArrayValue $self) {
		return clone $self;
	}

	public static function random(ArrayValue $self) {

		$key = array_rand($self->value);
		return $self->value[$key];

	}

	public static function shuffle(ArrayValue $self) {

		shuffle($self->value);
		return $self;

	}

	public static function map(ArrayValue $self, FuncValue $fn) {

		$result = [];
		foreach ($self->value as $k => $v) {
			$result[$k] = $fn->invoke([$v]);
		}

		return new ArrayValue($result);

	}

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
