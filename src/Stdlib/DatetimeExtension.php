<?php

declare(strict_types=1);

namespace Smuuf\Primi\StdLib;

use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Extensions\Extension;

class DatetimeExtension extends Extension {

	/**
	 * Returns high-resolution monotonic time. It is an arbitrary number that
	 * keeps increasing by 1 every second.
	 */
	public static function time_monotonic(): NumberValue {
		return new NumberValue((string) Func::hrtime());
	}

	/**
	 * Returns high-resolution UNIX time.
	 */
	public static function time_unix(): NumberValue {
		return new NumberValue((string) \microtime(\true));
	}

}
