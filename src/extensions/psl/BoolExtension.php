<?php

namespace Smuuf\Primi\Psl;

use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Extension;

class BoolExtension extends Extension {

	public static function to_bool(Value $value): BoolValue {
		return new BoolValue((bool) $value->value);
	}

}
