<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use Smuuf\Primi\Values\AbstractValue;

use Smuuf\StrictObject;

/**
 * Helper for accessing variables from two scopes via a single interface.
 *
 * First scope is acting as a primary, the second one acts as a fallback, if
 * the variable is not found in the primary scope.
 *
 * This is used in REPL.
 */
class ScopeComposite {

	use StrictObject;

	public function __construct(
		private Scope $a,
		private Scope $b,
	) {}

	/**
	 * Get a variable by its name.
	 */
	public function getVariable(string $name): ?AbstractValue {

		return $this->a->getVariable($name)
			?? $this->b->getVariable($name);

	}

	/**
	 * Returns a dictionary [var_name => AbstractValue] of all variables present
	 * in both scopes.
	 *
	 * @return array<string, AbstractValue>
	 */
	public function getVariables(bool $includeParents = \false): array {

		// Seemingly reversed 'a' vs 'b', but it's ok - variables from 'a'
		// should overwrite variables from 'b', because 'a' has priority.
		return array_merge(
			$this->b->getVariables($includeParents),
			$this->a->getVariables($includeParents),
		);

	}

}
