<?php

declare(strict_types=1);

namespace Smuuf\Primi\Extensions;

use \Smuuf\Primi\Helpers\Types;
use \Smuuf\Primi\Values\FuncValue;
use \Smuuf\Primi\Helpers\ValueFriends;

abstract class TypeExtension extends ValueFriends {

	final private function __construct() {
		// Disallow instantiation.
	}

	/**
	 * @return array<string, FuncValue> Dict array containing Primi functions
	 * which represent class methods.
	 */
	public static function execute(): array {
		return Types::prepareTypeMethods(
			MethodExtractor::extractMethods(new static),
		);
	}

}
