<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use Smuuf\Primi\Cli\Term;
use Smuuf\StrictObject;

abstract class Logger {

	use StrictObject;

	private static bool $enabled = \false;

	public static function enable(bool $state = \true): void {
		self::$enabled = $state;
	}

	public static function debug(string $msg): void {

		if (!self::$enabled) {
			return;
		}

		Term::stderr(Term::debug($msg));

	}

}
