<?php

declare(strict_types=1);

namespace Smuuf\Primi\Psl;

use \Smuuf\Primi\Structures\StringValue;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Structures\RegexValue;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Extension;

class RegexExtension extends Extension {

	/**
	 * Regular expression match. Returns the first matching string. Otherwise
	 * returns false.
	 */
	public static function regex_match(
		RegexValue $regex,
		StringValue $haystack
	): Value {

		if (!\preg_match($regex->value, $haystack->value, $matches)) {
			return new BoolValue(false);
		}

		return new StringValue($matches[0]);

	}

}
