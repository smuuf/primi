<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\Modules;

use \Smuuf\Primi\Extensions\PrimiFunc;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Values\NullValue;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Modules\NativeModule;
use \Smuuf\Primi\Structures\CallArgs;
use \Smuuf\Primi\Modules\AllowedInSandboxTrait;

return new
/**
 * Functions and tools for basic time-related operations.
 */
class extends NativeModule {

	use AllowedInSandboxTrait;

	/**
	 * Returns high-resolution monotonic time. It is an arbitrary number that
	 * keeps increasing by 1 every second.
	 */
	#[PrimiFunc]
	public static function monotonic(): NumberValue {
		return new NumberValue((string) Func::monotime());
	}

	/**
	 * Returns current high-resolution UNIX time.
	 */
	#[PrimiFunc]
	public static function now(): NumberValue {
		return new NumberValue((string) \microtime(\true));
	}

	/**
	 * Sleep specified number of seconds.
	 */
	#[PrimiFunc]
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
	 */
	#[PrimiFunc]
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

	/**
	 * Return string UNIX timestamp to a string representation specified by
	 * format. If timestamp is provided, current time will be used.
	 */
	#[PrimiFunc(callConv: PrimiFunc::CONV_CALLARGS)]
	public static function format(
		CallArgs $args
	): StringValue {

		[$format, $now] = $args->extractPositional(2, 1);

		if ($now) {
			Func::allow_argument_types(1, $now, NumberValue::class);
			$now = (int) $now->getStringValue();
		} else {
			$now = \time();
		}

		Func::allow_argument_types(2, $format, StringValue::class);
		$format = $format->getStringValue();

		return Interned::string(\date($format, $now));

	}

};
