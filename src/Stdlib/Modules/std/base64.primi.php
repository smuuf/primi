<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\Modules;

use Smuuf\Primi\Extensions\PrimiFunc;
use Smuuf\Primi\Values\StringValue;
use Smuuf\Primi\Helpers\Interned;
use Smuuf\Primi\Modules\NativeModule;
use Smuuf\Primi\Modules\AllowedInSandboxTrait;

return new
/**
 * Encoding and decoding [`base64`](https://en.wikipedia.org/wiki/Base64).
 */
class extends NativeModule {

	use AllowedInSandboxTrait;

	/**
	 * Return [`base64`](https://en.wikipedia.org/wiki/Base64)
	 * representation of a `string` value as `number`.
	 *
	 * ```js
	 * base64.encode('hello there, fellow kids') == "aGVsbG8gdGhlcmUsIGZlbGxvdyBraWRz"
	 * ```
	 */
	#[PrimiFunc]
	public static function encode(StringValue $data): StringValue {
		return Interned::string(\base64_encode($data->value));
	}

	/**
	 * Return [`base64`](https://en.wikipedia.org/wiki/Base64)
	 * representation of a `string` value as `number`.
	 *
	 * ```js
	 * base64.encode('hello there, fellow kids') == "aGVsbG8gdGhlcmUsIGZlbGxvdyBraWRz"
	 * ```
	 */
	#[PrimiFunc]
	public static function decode(StringValue $data): StringValue {

		$result = \base64_decode($data->value, \true);
		if ($result === \false) {
			Exceptions::piggyback(
				StaticExceptionTypes::getRuntimeErrorType(),
				"Failed to decode base64 string",
			);
		}

		return Interned::string($result);

	}

};
