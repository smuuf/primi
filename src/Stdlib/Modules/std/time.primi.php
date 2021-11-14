<?php

namespace Smuuf\Primi\Stdlib\Modules;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Values\NullValue;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Modules\NativeModule;
use \Smuuf\Primi\Stdlib\StaticTypes;
use \Smuuf\Primi\Stdlib\TypeExtensions\DateTypeExtension;
use \Smuuf\Primi\Values\TypeValue;

/**
 * Native 'std.time' module.
 */
return new class extends NativeModule {

	public function execute(Context $ctx): array {

		return [
			'Date' => new TypeValue(
				'Date',
				StaticTypes::getObjectType(),
				DateTypeExtension::execute(),
			),
		];

	}

	/**
	 * Returns high-resolution monotonic time. It is an arbitrary number that
	 * keeps increasing by 1 every second.
	 *
	 * @primi.function(no-stack)
	 */
	public static function monotonic(): NumberValue {
		return new NumberValue((string) Func::monotime());
	}

	/**
	 * Returns high-resolution UNIX time.
	 *
	 * @primi.function(no-stack)
	 */
	public static function unix(): NumberValue {
		return new NumberValue((string) \microtime(\true));
	}

	/**
	 * Sleep specified number of seconds.
	 *
	 * @primi.function(no-stack)
	 */
	public static function sleep(NumberValue $duration): NullValue {

		$d = $duration->value;
		if (Func::is_round_int($d)) {
			\sleep((int) $duration->value);
		} else {
			\usleep((int) ($duration->value * 1_000_000));
		}

		return Interned::null();

	}

	/**
	 * Return UNIX timestamp from human readable string.
	 *
	 * @see https://www.php.net/manual/en/function.strtotime.php
	 *
	 * @primi.function(no-stack)
	 */
	public static function from_string(StringValue $when): NumberValue {

		$when = $when->value;
		if (!\trim($when)) {
			throw new RuntimeError("Cannot convert empty string into UNIX timestamp");
		}

		$ts = \strtotime($when);
		if ($ts === \false) {
			throw new RuntimeError("Cannot convert '$when' into UNIX timestamp");
		}

		return new NumberValue((string) $ts);

	}

};
