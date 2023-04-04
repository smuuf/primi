<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use Smuuf\Primi\Context;
use Smuuf\Primi\MagicStrings;
use Smuuf\Primi\Ex\EngineError;
use Smuuf\Primi\Values\FuncValue;
use Smuuf\Primi\Values\TypeValue;
use Smuuf\Primi\Values\NullValue;
use Smuuf\Primi\Values\MethodValue;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Structures\CallArgs;

abstract class Types {

	public static function isSameType(
		?TypeValue $typeA,
		?TypeValue $typeB,
	): bool {

		if (!$typeA || !$typeB) {
			return \false;
		}

		return $typeA === $typeB;

	}

	public static function isSubtypeOf(
		?TypeValue $childType,
		?TypeValue $parentType,
	): bool {

		if (!$childType || !$parentType) {
			return \false;
		}

		do {
			if ($childType === $parentType) {
				return \true;
			}
		} while ($childType = $childType->getParentType());

		return \false;

	}

	public static function isInstanceOf(
		AbstractValue $object,
		TypeValue $type,
	): bool {

		$objectType = $object->getType();

		do {
			if ($objectType === $type) {
				return \true;
			}
		} while ($objectType = $objectType->getParentType());

		return \false;

	}

	/**
	 * Handles special treating of attributes that will represent attributes
	 * of a type object.
	 *
	 * For example the `__new__` method must be converted from ordinary method
	 * to static method for the type system and object model to work correctly.
	 *
	 * @param array<string, AbstractValue> $methods
	 * @return array<string, AbstractValue>
	 */
	public static function prepareTypeMethods(
		array $methods,
	): array {

		static $methodNameNew = MagicStrings::MAGICMETHOD_NEW;

		if (isset($methods[$methodNameNew])) {
			$methods[$methodNameNew] = new MethodValue(
				$methods[$methodNameNew]->getCoreValue(),
				isStatic: true,
			);
		}

		return $methods;

	}

	/**
	 * Lookup and return attr from a type object - or its parents.
	 *
	 * If the `$bind` argument is specified and the attr is a function, this
	 * returns a new `FuncValue` with partial arguments having the object
	 * in the `$bind` argument (this is how Primi handles object methods -
	 * the instance object is bound as the first argument of the function its
	 * type object provides).
	 *
	 * @return AbstractValue|null
	 */
	public static function attrLookup(
		?TypeValue $typeObject,
		string $attrName,
		?AbstractValue $bind = \null,
	) {

		if (!$typeObject) {
			return \null;
		}

		//
		// Try attr access on the type object itself.
		//
		// Example - Accessing `SomeClass.some_attr`:
		// Try if there's `some_attr` attribute in the SomeClass type itself.
		//

		if ($value = $typeObject->rawAttrGet($attrName)) {
			return $bind && $value instanceof FuncValue
				? $value->withBind($bind)
				: $value;
		}

		//
		// If the type object itself doesn't have this attr, try inheritance -
		// look for the attr in the parent type objects.
		//

		while ($typeObject = $typeObject->getParentType()) {
			if ($value = $typeObject->rawAttrGet($attrName)) {
				return $bind && $value instanceof FuncValue
					? $value->withBind($bind)
					: $value;
			}
		}

		return \null;

	}

	/**
	 * Converts PHP class names to Primi type names represented as string.
	 * Throws an exception if any PHP class name doesn't represent a Primi type.
	 *
	 * @param array<class-string|AbstractValue> $types
	 */
	public static function php_classes_to_primi_types(array $types): string {

		$primiTypes = \array_map(function($class) {

			// Resolve PHP nulls as Primi nulls.
			if (\is_string($class) && \strtolower($class) === 'null') {
				return NullValue::TYPE;
			}

			return TypeResolver::resolve($class);

		}, $types);

		return \implode('|', $primiTypes);

	}


	/**
	 * Returns array of Primi value types (PHP class names) of parameters
	 * for a PHP function of which the \ReflectionFunction is provided.
	 *
	 * In another words: This function returns which Primi types a PHP function
	 * expects.
	 *
	 * @return array<string>
	 * @throws EngineError
	 */
	public static function check_allowed_parameter_types_of_function(
		\ReflectionFunction $rf
	): array {

		$types = [];
		foreach ($rf->getParameters() as $rp) {

			$invalid = \false;
			$type = $rp->getType();

			if ($type === \null) {
				$invalid = 'Type must be specified';
			} else {

				// See https://github.com/phpstan/phpstan/issues/3886#issuecomment-699599667
				if (!$type instanceof \ReflectionNamedType) {
					$invalid = "Union types not yet supported";
				} else {

					$typeName = $type->getName();

					// a) Invalid if not hinting some AbstractValue class or its descendants.
					// b) Invalid if not hinting the Context class.
					if (\is_a($typeName, AbstractValue::class, \true)
						|| \is_a($typeName, Context::class, \true)
						|| \is_a($typeName, CallArgs::class, \true)
					) {
						$types[] = $typeName;
					} else {
						$invalid = "Type '$typeName' is not an allowed type";
					}

				}

			}

			if ($invalid) {

				$declaringClass = $rp->getDeclaringClass();
				$className = $declaringClass
					? $declaringClass->getName()
					: \null;

				$fnName = $rp->getDeclaringFunction()->getName();
				$paramName = $rp->getName();
				$paramPosition = $rp->getPosition();
				$fqn = $className ? "{$className}::{$fnName}()" : "{$fnName}()";

				$msg = "Parameter {$paramPosition} '\${$paramName}' for function {$fqn} "
					. "is invalid: $invalid";

				throw new EngineError($msg);

			};

		}

		return $types;

	}


	/**
	 * Return true if the value passed as first argument is any of the types
	 * passed as the rest of variadic arguments.
	 *
	 * We're using this helper e.g. in value methods for performing easy
	 * checks against allowed set of types of values. If PHP ever supports union
	 * types, I guess this helper method might become unnecessary (?).
	 *
	 */
	public static function is_any_of_types(?AbstractValue $value, string ...$types): bool {

		// If any of the "instanceof" checks is true,
		// the type is allowed - return without throwing exception.
		foreach ($types as $type) {
			if ($value instanceof $type) {
				return \true;
			}
		}

		return \false;

	}

}
