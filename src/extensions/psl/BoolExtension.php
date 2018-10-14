<?php

namespace Smuuf\Primi\Psl;

use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Extension;

class BoolExtension extends Extension {

	public static function bool_not(BoolValue $value): BoolValue {
		return new BoolValue(!$value->value);
	}

	public static function bool_and(BoolValue $l, BoolValue $r): BoolValue {
		return new BoolValue($l->value && $r->value);
	}

}
