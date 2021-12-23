<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Values\NullValue;
use \Smuuf\Primi\Values\RegexValue;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Values\StringValue;

use \Smuuf\StrictObject;

/**
 * Helper factory for building and interning primitive types.
 *
 * Primitive types are:
 * - null
 * - bool
 * - number
 * - string
 * - regex
 *
 * NOTE: Most typehints are in docstrings and not in PHP code for better
 * performance.
 */
abstract class Interned {

	use StrictObject;

	/**
	 * Storage for interned instances of null objects.
	 * @var NullValue
	 */
	private static $internedNull;

	/**
	 * Storage for interned instances of "false" bool object.
	 *
	 * By storing false/true separately and not under the 1/0 key, we save one
	 * array access operation each time we will be returning true or false bool
	 * object.
	 *
	 * @var BoolValue
	 */
	private static $internedBoolFalse;

	/**
	 * Storage for interned instances of "true" objects.
	 *
	 * By storing false/true separately and not under the 1/0 key, we save one
	 * array access operation each time we will be returning true or false bool
	 * object.
	 *
	 * @var BoolValue
	 */
	private static $internedBoolTrue;

	/**
	 * Storage for interned instances of number objects.
	 * @var array<string, NumberValue>
	 */
	private static $internedNumber = [];

	/**
	 * Storage for interned instances of string objects.
	 * @var array<string, StringValue>
	 */
	private static $internedString = [];

	/**
	 * Storage for interned instances of regex objects.
	 * @var array<string, RegexValue>
	 */
	private static $internedRegex = [];

	/**
	 * Initialize several known possible objects.
	 *
	 * This saves us one null-check when building objects for these
	 * super-primitive values.
	 */
	public static function init(): void {

		self::$internedNull = new NullValue;
		self::$internedBoolFalse = new BoolValue(\false);
		self::$internedBoolTrue = new BoolValue(\true);

	}

	/**
	 * @return NullValue
	 */
	public static function null() {
		return self::$internedNull;
	}

	/**
	 * @return BoolValue
	 */
	public static function bool(bool $truth) {

		if ($truth) {
			return self::$internedBoolTrue;
		} else {
			return self::$internedBoolFalse;
		}

	}

	/**
	 * @return NumberValue
	 */
	public static function number(string $number) {

		// Numbers up to 8 characters will be interned.
		if (\strlen($number) <= 8) {
			return self::$internedNumber[$number]
				?? (self::$internedNumber[$number] = new NumberValue($number));
		}

		return new NumberValue($number);

	}

	/**
	 * @return StringValue
	 */
	public static function string(string $str) {

		// Strings up to 64 characters will be interned.
		if (\strlen($str) <= 64) {
			return self::$internedString[$str]
				?? (self::$internedString[$str] = new StringValue($str));
		}

		return new StringValue($str);

	}

	/**
	 * @return RegexValue
	 */
	public static function regex(string $regex) {

		// Regexes up to 64 characters will be interned.
		if (\strlen($regex) <= 64) {
			return self::$internedRegex[$regex]
				?? (self::$internedRegex[$regex] = new RegexValue($regex));
		}

		return new RegexValue($regex);

	}

}

Interned::init();
