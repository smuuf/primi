<?php

declare(strict_types=1);

namespace Smuuf\Primi\Psl;

use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Structures\StringValue;
use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\Structures\Value;

use function \Smuuf\Primi\Helpers\allow_argument_types as primifn_allow_argument_types;

class HashExtension extends Extension {

	public static function hash_md5(Value $val): StringValue {

		primifn_allow_argument_types(
			0,
			$val,
			StringValue::class, NumberValue::class
		);

		$hash = md5((string) $val->value);
		return new StringValue($hash);

	}

	public static function hash_sha256(Value $val): StringValue {

		primifn_allow_argument_types(
			0,
			$val,
			StringValue::class, NumberValue::class
		);

		$hash = hash('sha256', (string) $val->value);
		return new StringValue($hash);
	}

}
