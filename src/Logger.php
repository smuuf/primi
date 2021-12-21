<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use \Smuuf\StrictObject;

abstract class Logger {

	use StrictObject;

	private static bool $enabled = \false;

	public static function enable(bool $state = \true): void {
		self::$enabled = $state;
	}

	public static function debug(string $msg): void {

		if (self::$enabled && \ftell(\STDERR)) {
			\fwrite(\STDERR, "DEBUG: $msg\n");
		}

	}

}
