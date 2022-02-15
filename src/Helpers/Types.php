<?php

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Values\FuncValue;
use \Smuuf\Primi\Values\TypeValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Structures\CallArgs;
use \Smuuf\Primi\Values\NullValue;

abstract class Types {

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
	public static function attr_lookup(
		TypeValue $typeObject,
		string $attrName,
		?AbstractValue $bind = \null
	) {

		//
		// Try attr access on the type object itself.
		//
		// Example - Accessing `SomeClass.some_attr`:
		// Try if there's `some_attr` attribute in the SomeClass type itself.
		//

		if ($value = $typeObject->rawAttrGet($attrName)) {
			if ($bind && $value instanceof FuncValue) {
				$args = new CallArgs([$bind]);
				return new FuncValue($value->getInternalValue(), $args);
			}
			return $value;
		}

		//
		// If the type object itself doesn't have this attr, try inheritance -
		// look for the attr in the parent type objects.
		//

		while ($typeObject = $typeObject->getParentType()) {
			if ($value = $typeObject->rawAttrGet($attrName)) {
				if ($bind && $value instanceof FuncValue) {
					$args = new CallArgs([$bind]);
					return new FuncValue($value->getInternalValue(), $args);
				}
				return $value;
			}
		}

		return \null;

	}

	/**
	 * Converts PHP class names to Primi type names represented as string.
	 * Throws an exception if any PHP class name doesn't represent a Primi type.
	 *
	 * @param array<string> $types
	 */
	public static function php_classes_to_primi_types(array $types): string {

		$primiTypes = \array_map(function($class) {

			// Resolve PHP nulls as Primi nulls.
			if (strtolower($class) === 'null') {
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
