<?php

namespace Smuuf\Primi\Psl;

use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Structures\NumberValue;

class NumberExtension extends Extension {

	public static function round(NumberValue $num, NumberValue $precision = \null): NumberValue {
		return new NumberValue(\round($num->value, $precision ? $precision->value : 0));
	}

	public static function abs(NumberValue $num): NumberValue {
		return new NumberValue(abs($num->value));
	}

	public static function ceil(NumberValue $num): NumberValue {
		return new NumberValue(\ceil($num->value));
	}

	public static function floor(NumberValue $num): NumberValue {
		return new NumberValue(\floor($num->value));
	}

	public static function sqrt(NumberValue $num): NumberValue {
		return new NumberValue(\sqrt($num->value));
	}

	public static function pow(NumberValue $num, NumberValue $power = \null): NumberValue {
		return new NumberValue($num->value ** ($power === \null ? 2 : $power->value));
	}

	public static function sin(NumberValue $num): NumberValue {
		return new NumberValue(\sin($num->value));
	}

	public static function cos(NumberValue $num): NumberValue {
		return new NumberValue(\cos($num->value));
	}

	public static function tan(NumberValue $num): NumberValue {
		return new NumberValue(\tan($num->value));
	}

	public static function atan(NumberValue $num): NumberValue {
		return new NumberValue(\atan($num->value));
	}

}
