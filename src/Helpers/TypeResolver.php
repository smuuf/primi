<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Ex\EngineInternalError;

abstract class TypeResolver {

	/**
	 * Some classes used for representing Primi objects have "unfriendly"
	 * values of their "TYPE" class constants.
	 *
	 * If we, for example, want to display error message about wrong type
	 * of argument passed into a native PHP function (called from Primi) when
	 * a basic "AbstractValue" class is typehinted (because that PHP class
	 * is a base class for other PHP classes representing Primi objects), we
	 * want to display "object" instead of AbstractValue's "__undefined__" TYPE.
	 */
	private const OVERRIDE_TYPES = [
		\Smuuf\Primi\Values\AbstractValue::class => 'object',
		\Smuuf\Primi\Values\InstanceValue::class => 'object',
	];

	public static function resolve(string $class): string {

		$class = \ltrim($class, '\\');
		if ($overridden = (self::OVERRIDE_TYPES[$class] ?? \null)) {
			return $overridden;
		}

		if (\is_subclass_of($class, \Smuuf\Primi\Values\AbstractValue::class, \true)) {
			return $class::TYPE;
		}

		throw new EngineInternalError("Unable to resolve basic type name for class '$class'");

	}

}
