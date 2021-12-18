<?php

declare(strict_types=1);

namespace Smuuf\Primi\Parser;

use \Smuuf\StrictObject;

abstract class GrammarHelpers {

	use StrictObject;

	const RESERVED_WORDS = [
		'false', 'true', 'null', 'if', 'else', 'return', 'for', 'and', 'or',
		'function', 'break', 'continue', 'while', 'try', 'catch', 'not', 'in',
		'import'
	];

	public static function isReservedWord(string $name): bool {
		return \in_array($name, self::RESERVED_WORDS, \true);
	}

	public static function isValidName(string $name): bool {
		return (bool) \preg_match('#(?:[a-zA-Z_][a-zA-Z0-9_]*)#S', $name);
	}

}
