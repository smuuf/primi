<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use \Smuuf\Primi\Ex\EngineInternalError;
use \Smuuf\Primi\Values\AbstractValue;

use \Smuuf\StrictObject;

/**
 * A structure for representing a variable scope - storage for variables.
 */
class Scope {

	use StrictObject;

	/**
	 * Flag for standard, ordinary scopes.
	 */
	public const TYPE_STANDARD = 0;

	/**
	 * Flag to distinguish scopes representing a class scope.
	 *
	 * This is used to tell function definitions that they should not set their
	 * scopes' parent to this scope. In another words: Methods of a class
	 * should _not_ have direct access to the scope of the class. Those
	 * should be accessed only via the "self" special variable.
	 *
	 * @const int
	 */
	public const TYPE_CLASS = 1;

	/** @var array<string, AbstractValue> Variable pool. */
	private $variables = [];

	/** Parent scope, if any. */
	private ?Scope $parent = \null;

	/** Scope type. */
	private int $type = self::TYPE_STANDARD;

	/**
	 * @param array<string, AbstractValue> $variables
	 */
	public function __construct(
		array $variables = [],
		int $type = self::TYPE_STANDARD
	) {
		$this->setVariables($variables);
		$this->type = $type;
	}

	/**
	 * Return type of the scope.
	 */
	public function getType(): int {
		return $this->type;
	}

	public function setParent(self $parent): void {

		if ($this === $parent) {
			throw new EngineInternalError("Scope cannot have itself as parent");
		}

		$this->parent = $parent;

	}

	public function getParent(): ?Scope {
		return $this->parent;
	}

	/**
	 * Get a variable by its name.
	 *
	 * If the variable is missing in current scope, look the variable up in the
	 * parent scope, if there's any.
	 */
	public function getVariable(string $name): ?AbstractValue {

		return $this->variables[$name]
			// Recursively up, if there's a parent scope.
			?? (
				$this->parent !== \null
				? $this->parent->getVariable($name)
				: \null
			);

	}

	/**
	 * Returns a dictionary [var_name => AbstractValue] of all variables present
	 * in this scope.
	 *
	 * If the `$includeParents` argument is `true`, variables from parent scopes
	 * will be included too (variables in child scopes have priority over those
	 * from parent scopes).
	 *
	 * @return array<string, AbstractValue>
	 */
	public function getVariables(bool $includeParents = \false): array {

		$fromParents = ($includeParents && $this->parent !== \null)
			// Recursively up, if there's a parent scope.
			? $this->parent->getVariables($includeParents)
			: [];

		return $this->variables + $fromParents;

	}

	/**
	 * @return void
	 */
	public function setVariable(string $name, AbstractValue $value) {
		$this->variables[$name] = $value;
	}

	/**
	 * Set multiple variables to the scope.
	 *
	 * @param array<string, AbstractValue> $pairs
	 * @return void
	 */
	public function setVariables(array $pairs) {
		// NOTE: array_merge() instead of '+' keeps original and expected order.
		$this->variables = \array_merge($this->variables, $pairs);
	}

}
