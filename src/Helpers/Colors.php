<?php

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Helpers\Traits\StrictObject;

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

	public static function getMaybe(
		bool $apply,
		string $string,
		bool $reset = true
	): string {

		$handler = $apply ? [self::class, 'handler'] : fn() => '';
		return self::apply($string, $handler, $reset);

	}

	public static function get(string $string, bool $reset = true): string {
		return self::apply($string, [self::class, 'handler'], $reset);
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

	private static function handler(array $m): string {

		if (\getenv('NO_COLOR') !== \false) {
			return '';
		}

		$color = $m[1];

		if (isset(self::COLORS[$color])) {
			return \sprintf(self::COLOR_FORMAT, self::COLORS[$color]);
		}

		throw new \LogicException("Unknown color '$color'");

	}

}
