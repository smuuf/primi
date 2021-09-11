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

	/** @var array<string, AbstractValue> Variable pool. */
	private $variables = [];

	/** Parent scope, if any. */
	private ?Scope $parent = \null;

	public function __construct(array $variables = []) {
		$this->setVariables($variables);
	}

	final public function setParent(self $parent): void {

		if ($this === $parent) {
			throw new EngineInternalError("Scope cannot have itself as parent");
		}

		$this->parent = $parent;

	}

	/**
	 * Get a variable by its name.
	 *
	 * If the variable is missing in current scope, look the variable up in the
	 * parent scope, if there's any.
	 */
	final public function getVariable(string $name): ?AbstractValue {

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
	 */
	final public function getVariables(bool $includeParents = \false): array {

		$fromParents = ($includeParents && $this->parent !== \null)
			// Recursively up, if there's a parent scope.
			? $this->parent->getVariables($includeParents)
			: [];

		return $this->variables + $fromParents;

	}

	public function setVariable(string $name, AbstractValue $value) {
		$this->variables[$name] = $value;
	}

	/**
	 * Set multiple variables to the scope.
	 *
	 * @param array<string, AbstractValue> $pairs
	 */
	public function setVariables(array $pairs) {
		$this->variables = $pairs + $this->variables;
	}

}
