<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Location;
use \Smuuf\Primi\MagicStrings;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Stdlib\BuiltinTypes;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\AbstractBuiltinValue;
use \Smuuf\Primi\Helpers\Types;
use \Smuuf\Primi\Structures\CallArgs;

/**
 * Instance of this PHP class represents a Primi object which acts as type/class
 * in Primi.
 *
 * Everything in Primi is an object -> types/classes themselves are also
 * objects (instances of some type/class XYZ) -> and that type/class XYZ is
 * represented by this `TypeValue` PHP class.
 *
 * Therefore, the type "type" is an instance of itself.
 */
class TypeValue extends AbstractBuiltinValue {

	public const TYPE = "type";

	/**
	 * @param string $name Name of the type.
	 * @param array<string, AbstractValue> $attrs
	 * @param bool $isFinal Final type/class cannot be used as a parent type.
	 * @param ?TypeValue Parent Primi type of this type.
	 */
	public function __construct(
		protected string $name,
		protected ?TypeValue $parent = \null,
		protected array $attrs = [],
		protected bool $isFinal = \true,
		protected bool $isMutable = \false,
	) {

		if ($this->parent?->isFinal()) {
			throw new RuntimeError("Class '{$this->parent->getName()}' cannot be used as a parent class");
		}

	}

	public function getType(): TypeValue {
		return BuiltinTypes::getTypeType();
	}

	public function attrGet(string $name): ?AbstractValue {

		// Try to lookup the attr in this objects attrs. If not found, search
		// the attr in its parent types. And don't bind any found (and then
		// returned) functions to this type object instance.
		// This means "SomeType.some_method()" won't get "SomeType" as
		// first argument - which is what we want - we're accessing the function
		// through a type, not from an instance.
		if ($attr = Types::attr_lookup($this, $name)) {
			return $attr;
		}

		// If the attr was not found in this type object nor its parent types,
		// search through the type of this type object (its metatype/metaclass).
		// If we find a function, we'll bind it to this type object instance -
		// that's because in this case this type object really acts as an
		// instance of its type (metatype).
		return Types::attr_lookup($this->getType(), $name, $this);

	}

	/**
	 * Assign an attr to the type instance. Immutable types (those are the
	 * basic types that are in the core of Primi) can't be mutated (as they
	 * can be shared among different instances of Primi engine within a single
	 * PHP runtime). Userland types can be mutable.
	 */
	public function attrSet(string $name, AbstractValue $value): bool {

		if (!$this->isMutable) {
			return false;
		}

		$this->attrs[$name] = $value;
		return \true;

	}


	/**
	 * Unified method to return a type object any type object *INHERITS FROM*,
	 * which is the 'object' object. Easy.
	 */
	public function getParentType(): ?TypeValue {
		return $this->parent;
	}

	public function getStringRepr(): string {
		return "<type '{$this->name}'>";
	}

	/**
	 * Get name of the type this object represents - as string.
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * Is this Primi type/class forbidden to be a parent of another type/class?
	 */
	public function isFinal(): bool {
		return $this->isFinal;
	}

	public function invoke(
		Context $context,
		?CallArgs $args = \null,
		?Location $callsite = \null
	): ?AbstractValue {

		// Special case: If this type object is _the_ "type" type object.
		if ($this->name === self::TYPE) {
			if ($fn = Types::attr_lookup($this, MagicStrings::MAGICMETHOD_CALL)) {
				return $fn->invoke($context, $args, $callsite);
			}
		}

		// The other possibility: This type object represents other type
		// than the "type" type itself - and calling this object should
		// represent instantiation of new object that will have this type.
		if ($fn = Types::attr_lookup($this, MagicStrings::MAGICMETHOD_NEW, $this)) {
			$newArgs = $args
				? new CallArgs([$this, ...$args->getArgs()], $args->getKwargs())
				: new CallArgs([$this]);
			$result = $fn->invoke($context, $newArgs, $callsite);

			if ($init = $result->attrGet(MagicStrings::MAGICMETHOD_INIT)) {
				$init->invoke($context, $args);
			}

			return $result;
		}

		return \null;

	}

	public function dirItems(): ?array {

		$fromParents = [];
		$t = $this;
		while ($t = $t->getParentType()) {
			$fromParents = [...$t->dirItems(), ...$fromParents];
		}

		return \array_unique([
			...$fromParents,
			...\array_keys($this->attrs),
		]);

	}

}
