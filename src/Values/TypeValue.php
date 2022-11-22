<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Location;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Stdlib\StaticTypes;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\AbstractNativeValue;
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
 *
 * @internal
 */
final class TypeValue extends AbstractNativeValue {

	public const TYPE = "type";

	/** Name of the type. */
	protected string $name;

	/** A parent Primi type of this type. */
	protected ?TypeValue $parent;

	/** Final type/class cannot be used as a parent type. */
	protected bool $isFinal;

	/**
	 * @param array<string, AbstractValue> $attrs
	 */
	public function __construct(
		string $name,
		?TypeValue $parent = \null,
		array $attrs = [],
		bool $isFinal = \true
	) {

		$this->name = $name;
		$this->parent = $parent;
		$this->attrs = $attrs;
		$this->isFinal = $isFinal;

		if ($this->parent && $this->parent->isFinal()) {
			throw new RuntimeError("Class '{$this->parent->getName()}' cannot be used as a parent class");
		}

	}

	public function getType(): TypeValue {
		return StaticTypes::getTypeType();
	}

	public function attrGet(string $name): ?AbstractValue {
		return Types::attr_lookup($this, $name);
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
