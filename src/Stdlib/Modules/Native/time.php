<?php

namespace Smuuf\Primi\Stdlib\Modules;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Values\NullValue;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Extensions\Module;

/**
 * Native 'time' module.
 */
return new class extends Module {

	public function execute(Context $ctx): array {

		return [
			'monotonic' => [self::class, 'monotonic'],
			'unix' => [self::class, 'unix'],
			'sleep' => [self::class, 'sleep'],
			'from_string' => [self::class, 'from_string'],
		];

	}

	/**
	 * Returns high-resolution monotonic time. It is an arbitrary number that
	 * keeps increasing by 1 every second.
	 */
	public static function monotonic(): NumberValue {
		return new NumberValue((string) Func::monotime());
	}

	/**
	 * Returns high-resolution UNIX time.
	 */
	public static function unix(): NumberValue {
		return new NumberValue((string) \microtime(\true));
	}

	/**
	 * Sleep specified number of seconds.
	 */
	public static function sleep(NumberValue $duration): NullValue {

		$d = $duration->value;
		if (Func::is_round_int($d)) {
			\sleep((int) $duration->value);
		} else {
			\usleep((int) ($duration->value * 1_000_000));
		}

		return NullValue::build();

	}

	/**
	 * Return UNIX timestamp from human readable string.
	 *
	 * @see https://www.php.net/manual/en/function.strtotime.php
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
