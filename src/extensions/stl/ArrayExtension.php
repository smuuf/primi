<?php

namespace Smuuf\Primi\Stl;

use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\Structures\ArrayValue;
use \Smuuf\Primi\Structures\FuncValue;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\ErrorException;

class ArrayExtension extends Extension {

	public function length(ArrayValue $self): NumberValue {
		return new NumberValue(count($self->value));
	}

	public function copy(ArrayValue $self): ArrayValue {
		return clone $self;
	}

	public function random(ArrayValue $self): Value {

		$key = array_rand($self->value);
		return $self->value[$key];

	}

	public function shuffle(ArrayValue $self): ArrayValue {

		shuffle($self->value);
		return $self;

	}

	public function map(ArrayValue $self, FuncValue $fn): ArrayValue {

		$result = [];
		foreach ($self->value as $k => $v) {
			$result[$k] = $fn->invoke([$v]);
		}

		return new ArrayValue($result);

	}

	public function contains(ArrayValue $self, Value $value): BoolValue {

		// Let's search the $value object in $self's value (array of objects).
		return new BoolValue(\array_search($value, $self->value) !== \false);

	}

	public function push(ArrayValue $self, Value $value): Value {
		$self->value[] = $value;
		return $value;
	}

	public function pop(ArrayValue $self): Value {
		return \array_pop($self->value);
	}

}
