<?php

namespace Smuuf\Primi\Extensions;

use \Smuuf\Primi\Helpers\ValueFriends;
use \Smuuf\Primi\Helpers\MethodExtractor;

abstract class TypeExtension extends ValueFriends {

	/**
	 * @return array<string, AbstractValue|mixed> Dict array that represents the
	 * contents of the module.
	 */
	public static function execute(): array {
		return MethodExtractor::extractMethods(new static);
	}

}
