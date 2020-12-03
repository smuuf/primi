<?php

declare(strict_types=1);

namespace Smuuf\Primi\StdLib;

use \Smuuf\Primi\Extensions\Extension;
use \Smuuf\Primi\Values\StringValue;

class HashExtension extends Extension {

	/**
	 * Return [`md5` hash](https://en.wikipedia.org/wiki/MD5) representation
	 * of a `string` value as `string`.
	 *
	 * ```js
	 * hash_md5('hello') == '5d41402abc4b2a76b9719d911017c592'
	 * hash_md5('123') == '202cb962ac59075b964b07152d234b70'
	 * ```
	 */
	public static function hash_md5(StringValue $val): StringValue {

		$hash = \md5((string) $val->value);
		return StringValue::build($hash);

	}

	/**
	 * Return [`sha256` hash](https://en.wikipedia.org/wiki/SHA-2) representation
	 * of a `string` value as `string`.
	 *
	 * ```js
	 * hash_sha256('hello') == '2cf24dba5fb0a30e26e83b2ac5b9e29e1b161e5c1fa7425e73043362938b9824'
	 * hash_sha256('123') == 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3'
	 * ```
	 */
	public static function hash_sha256(StringValue $val): StringValue {

		$hash = \hash('sha256', (string) $val->value);
		return StringValue::build($hash);
	}

}
