<?php

namespace Smuuf\Primi\Stl;

use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\ErrorException;

class NumberExtension extends Extension {

	public function round(NumberValue $self, NumberValue $precision = \null): NumberValue {
		return new NumberValue(\round($self->value, $precision ? $precision->value : 0));
	}

	public function abs(NumberValue $self): NumberValue {
		return new NumberValue(abs($self->value));
	}

	public function ceil(NumberValue $self): NumberValue {
		return new NumberValue(\ceil($self->value));
	}

	public function floor(NumberValue $self): NumberValue {
		return new NumberValue(\floor($self->value));
	}

	public function sqrt(NumberValue $self): NumberValue {
		return new NumberValue(\sqrt($self->value));
	}

	public function pow(NumberValue $self, NumberValue $power = \null): NumberValue {
		return new NumberValue($self->value ** ($power === \null ? 2 : $power->value));
	}

	public function sin(NumberValue $self): NumberValue {
		return new NumberValue(\sin($self->value));
	}

	public function cos(NumberValue $self): NumberValue {
		return new NumberValue(\cos($self->value));
	}

	public function tan(NumberValue $self): NumberValue {
		return new NumberValue(\tan($self->value));
	}

	public function atan(NumberValue $self): NumberValue {
		return new NumberValue(\atan($self->value));
	}

}
