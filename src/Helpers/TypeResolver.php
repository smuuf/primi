<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use Smuuf\Primi\Ex\EngineInternalError;
use Smuuf\Primi\Values\TypeValue;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Values\InstanceValue;

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
		AbstractValue::class => 'object',
		InstanceValue::class => 'object',
	];

	/**
	 * Return type name for a Primi object or for PHP class which represents
	 * object of a basic native type (e.g. `StringValue`).
	 *
	 * @param class-string|AbstractValue $type
	 */
	public static function resolve($type): string {

		if (\is_string($type)) {

			// Return Primi type name based on passed PHP class name
			// representing a basic native Primi object.

			// Normalize PHP class name to a form without leading backslash.
			$type = \ltrim($type, '\\');
			if (isset(self::OVERRIDE_TYPES[$type])) {
				return self::OVERRIDE_TYPES[$type];
			}

			if (!\is_subclass_of($type, AbstractValue::class, \true)) {
				throw new EngineInternalError(
					"Unable to resolve Primi type name for PHP class '$type'");
			}

			return $type::TYPE;

		} elseif ($type instanceof TypeValue) {

			// Return Primi type name based on an actual Primi object (passed
			// as PHP object representing a Primi object).
			return $type->getName();

		}

		$type = \get_debug_type($type);
		throw new EngineInternalError(
			"Unable to resolve Primi type name from PHP value '$type'");

	}

}
