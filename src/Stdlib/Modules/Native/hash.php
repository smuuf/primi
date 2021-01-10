<?php

namespace Smuuf\Primi\Stdlib\Modules;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Extensions\Module;

/**
 * Native 'hash' module.
 */
return new class extends Module {

	public function execute(Context $ctx): array {

		return [
			'crc32' => [self::class, 'crc32'],
			'md5' => [self::class, 'md5'],
			'sha216' => [self::class, 'sha256'],
		];

	}

	/**
	 * Return [`crc32`](https://en.wikipedia.org/wiki/Cyclic_redundancy_check)
	 * representation of a `string` value as `number`.
	 *
	 * ```js
	 * crc32('hello') == 907060870
	 * crc32('123') == 2286445522
	 * ```
	 */
	public static function crc32(StringValue $val): NumberValue {
		return NumberValue::build((string) \crc32($val->value));
	}

	/**
	 * Return [`md5` hash](https://en.wikipedia.org/wiki/MD5) representation
	 * of a `string` value as `string`.
	 *
	 * ```js
	 * md5('hello') == '5d41402abc4b2a76b9719d911017c592'
	 * md5('123') == '202cb962ac59075b964b07152d234b70'
	 * ```
	 */
	public static function md5(StringValue $val): StringValue {
		return StringValue::build(\md5($val->value));
	}

	/**
	 * Return [`sha256` hash](https://en.wikipedia.org/wiki/SHA-2) representation
	 * of a `string` value as `string`.
	 *
	 * ```js
	 * sha256('hello') == '2cf24dba5fb0a30e26e83b2ac5b9e29e1b161e5c1fa7425e73043362938b9824'
	 * sha256('123') == 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3'
	 * ```
	 */
	public static function sha256(StringValue $val): StringValue {
		return StringValue::build(\hash('sha256', $val->value));
	}

};
