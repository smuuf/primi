<?php

namespace Smuuf\Primi\Extensions;

use \Smuuf\Primi\Values\FuncValue;
use \Smuuf\Primi\Helpers\ValueFriends;
use \Smuuf\Primi\Helpers\MethodExtractor;

abstract class TypeExtension extends ValueFriends {

	final private function __construct() {
		// Disallow instantiation.
	}

	/**
	 * @return array<string, FuncValue> Dict array containing Primi functions
	 * which represent class methods.
	 */
	public static function execute(): array {
		return MethodExtractor::extractMethods(new static);
	}

}
