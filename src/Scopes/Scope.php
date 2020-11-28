<?php

namespace Smuuf\Primi\Scopes;

use \Smuuf\Primi\Values\ValueFactory;
use \Smuuf\Primi\Values\AbstractValue;

/**
 * A default in-memory "variable scope" structure.
 */
class Scope extends AbstractScope {

	/** @var array<string, AbstractValue> Variable pool assigned to this frame. */
	protected $variables = [];

	public function setVariable(string $name, AbstractValue $value) {
		$this->variables[$name] = $value;
	}

	/**
	 * Set multiple variables to the scope using an array as parameter.
	 *
	 * @param array<string, AbstractValue> $pairs
	 */
	public function setVariables(array $pairs) {

		foreach ($pairs as $name => $value) {

			if (!$value instanceof AbstractValue) {
				$value = ValueFactory::buildAutomatic($value);
			}

			$this->setVariable($name, $value);

		}

	}

	public function fetchVariable(string $name): ?AbstractValue {
		return $this->variables[$name] ?? null;
	}

	public function fetchVariables(): array {
		return $this->variables;
	}

}
