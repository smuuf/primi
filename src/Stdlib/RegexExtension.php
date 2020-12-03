<?php

declare(strict_types=1);

namespace Smuuf\Primi\StdLib;

use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Values\RegexValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Extensions\Extension;

class RegexExtension extends Extension {

	/**
	 * Regular expression match. Returns the first matching string. Otherwise
	 * returns `false`.
	 *
	 * ```js
	 * rx"[xyz]+".match("abbcxxyzzdeef") == "xxyzz"
	 * ```
	 */
	public static function regex_match(
		RegexValue $regex,
		StringValue $haystack
	): AbstractValue {

		if (!\preg_match($regex->value, $haystack->value, $matches)) {
			return BoolValue::build(false);
		}

		return StringValue::build($matches[0]);

	}

}
