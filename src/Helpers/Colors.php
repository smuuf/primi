<?php

namespace Smuuf\Primi\Helpers;

use \Smuuf\StrictObject;

abstract class Colors {

	use StrictObject;

	const COLOR_FORMAT = "\033[%sm";
	const COLORS = [

		// Reset.
		'_' => '0',

		// Fore.
		'black' => '0;30',
		'darkgrey' => '1;30',
		'darkgray' => '1;30',
		'blue' => '0;34',
		'lightblue' => '1;34',
		'green' => '0;32',
		'lightgreen' => '1;32',
		'cyan' => '0;36',
		'lightcyan' => '1;36',
		'red' => '0;31',
		'lightred' => '1;31',
		'purple' => '0;35',
		'lightpurple' => '1;35',
		'brown' => '0;33',
		'yellow' => '0;33',
		'lightyellow' => '1;33',
		'lightgray' => '0;37',
		'lightgrey' => '0;37',
		'white' => '1;37',

		// Back.
		'-black' => '40',
		'-red' => '41',
		'-green' => '42',
		'-yellow' => '43',
		'-blue' => '44',
		'-magenta' => '45',
		'-cyan' => '46',
		'-lightgray' => '47',
		'-lightgrey' => '47',

		// Styles.

		'bold' => '1',
		'dim' => '2',
		'underline' => '4',
		'blink' => '5',
		'invert' => '7',

	];

	private static bool $noColor;

	public static function init(): void {

		// Disable colors if either of these is true ...

		// 1) Env var "NO_COLOR" contains a truthy value.
		// 2) Current STDOUT is _not_ a terminal (for example when output is
		// being piped elsewhere - for example into a file).
		self::$noColor = (
			(bool) \getenv('NO_COLOR')
			|| !posix_isatty(STDOUT)
		);

	}

	public static function get(
		string $string,
		bool $reset = true,
		bool $applyColors = true
	): string {

		$handler = $applyColors ? [self::class, 'handler'] : fn() => '';
		return self::apply($string, $handler, $reset);

	}

	private static function apply(
		string $string,
		callable $handler,
		bool $reset = true
	): string {

		// Insert reset character if we should reset styles on end.
		if ($reset) {
			$string .= '{_}';
		}

		return \preg_replace_callback(
			'#(?<!\\\\)\{([a-z_-][a-z-]*)\}#i',
			$handler,
			$string
		);

	}

	/**
	 * @param array<int, string> $matches
	 */
	private static function handler(array $matches): string {

		if (self::$noColor !== \false) {
			return '';
		}

		$color = $matches[1];

		if (isset(self::COLORS[$color])) {
			return \sprintf(self::COLOR_FORMAT, self::COLORS[$color]);
		}

		throw new \LogicException("Unknown color '$color'");

	}

}

Colors::init();
