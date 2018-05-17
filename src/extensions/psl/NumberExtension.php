<?php

namespace Smuuf\Primi\Psl;

use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\ErrorException;

class NumberExtension extends Extension {

	public function round() {

		return function(NumberValue $self, NumberValue $precision = \null): NumberValue {
			return new NumberValue(\round($self->value, $precision ? $precision->value : 0));
		};

	}

	public function abs() {

		return function(NumberValue $self): NumberValue {
			return new NumberValue(abs($self->value));
		};

	}

	public function ceil() {

		return function(NumberValue $self): NumberValue {
			return new NumberValue(\ceil($self->value));
		};

	}

	public function floor() {

		return function(NumberValue $self): NumberValue {
			return new NumberValue(\floor($self->value));
		};

	}

	public function sqrt() {

		return function(NumberValue $self): NumberValue {
			return new NumberValue(\sqrt($self->value));
		};

	}

	public function pow() {

		return function(NumberValue $self, NumberValue $power = \null): NumberValue {
			return new NumberValue($self->value ** ($power === \null ? 2 : $power->value));
		};

	}

	public function sin() {

		return function(NumberValue $self): NumberValue {
			return new NumberValue(\sin($self->value));
		};

	}

	public function cos() {

		return function(NumberValue $self): NumberValue {
			return new NumberValue(\cos($self->value));
		};

	}

	public function tan() {

		return function(NumberValue $self): NumberValue {
			return new NumberValue(\tan($self->value));
		};

	}

	public function atan() {

		return function(NumberValue $self): NumberValue {
			return new NumberValue(\atan($self->value));
		};

	}

}
