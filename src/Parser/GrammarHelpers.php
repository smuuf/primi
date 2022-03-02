<?php

declare(strict_types=1);

namespace Smuuf\Primi\Parser;

use \Smuuf\StrictObject;

abstract class GrammarHelpers {

	use StrictObject;

	const VALID_NAME_REGEX = '[a-zA-Z_][a-zA-Z0-9_]*';

	const RESERVED_WORDS = [
		'false', 'true', 'null', 'if', 'else', 'return', 'for', 'and', 'or',
		'function', 'break', 'continue', 'while', 'try', 'catch', 'not', 'in',
		'import'
	];

	public static function isReservedWord(string $name): bool {
		return \in_array($name, self::RESERVED_WORDS, \true);
	}

	public static function isValidName(string $name): bool {
		return (bool) \preg_match(
			sprintf('#^(?:%s)$#S', self::VALID_NAME_REGEX),
			$name
		);
	}

	public static function isSimpleAttrAccess(string $name): bool {
		return (bool) \preg_match(
			sprintf('#^(?:%1$s)(?:\.%1$s)*$#S', self::VALID_NAME_REGEX),
			$name
		);
	}

}
