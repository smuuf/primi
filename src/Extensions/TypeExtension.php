<?php

declare(strict_types=1);

namespace Smuuf\Primi\Extensions;

use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Types;
use \Smuuf\Primi\Helpers\ValueFriends;

abstract class TypeExtension extends ValueFriends {

	final private function __construct() {
		// Disallow instantiation.
	}

	/**
	 * @return array<string, AbstractValue> Dict array containing Primi
	 * function/method object that represent type/class methods.
	 */
	public static function execute(): array {
		return Types::prepareTypeMethods(
			MethodExtractor::extractMethods(new static),
		);
	}

}
