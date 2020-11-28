<?php

declare(strict_types=1);

namespace Smuuf\Primi\StdLib;

use \Smuuf\Primi\Extensions\Extension;
use \Smuuf\Primi\Values\NumberValue;

class DatetimeExtension extends Extension {

	/**
	 * Returns high-resolution monotonic time. It is an arbitrary number that
	 * keeps increasing by 1 every second.
	 */
	public static function time_monotonic(): NumberValue {

		// hrtime() is available only from PHP 7.3
		if (PHP_VERSION_ID < 73000) {
			return new NumberValue((string) \microtime(\true));
		}

		return new NumberValue((string) \hrtime(\true));

	}

	/**
	 * Returns high-resolution UNIX time.
	 */
	public static function time_unix(): NumberValue {
		return new NumberValue((string) \microtime(\true));
	}

}
