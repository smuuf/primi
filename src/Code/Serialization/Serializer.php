<?php

declare(strict_types=1);

namespace Smuuf\Primi\Code\Serialization;

use Smuuf\StrictObject;
use Smuuf\Primi\Code\Bytecode;
use Smuuf\Primi\Values\BoolValue;
use Smuuf\Primi\Values\NullValue;
use Smuuf\Primi\Values\NumberValue;
use Smuuf\Primi\Values\RegexValue;
use Smuuf\Primi\Values\StringValue;

/**
 * Bytecode (un)serializer.
 *
 * Serves as an intermediary when saving and loading bytecode to/from cache.
 */
class Serializer {

	use StrictObject;

	private const UNSERIALIZE_OPTIONS = [
		'allowed_classes' => [
			NumberValue::class,
			StringValue::class,
			BoolValue::class,
			NullValue::class,
			RegexValue::class,
			Bytecode::class,
		],
	];

	public static function serialize(Bytecode $bytecode): string {
		return serialize($bytecode);
	}

	public static function unserialize(string $string): Bytecode {
		return unserialize($string, options: self::UNSERIALIZE_OPTIONS);
	}

}
