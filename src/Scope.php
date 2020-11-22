<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;

/**
 * A default in-memory "variable scope" structure.
 */
class Scope extends AbstractScope {

	/** @var array<string, Value> Variable pool assigned to this frame. */
	protected $variables = [];

	public function setVariable(string $name, Value $value) {
		$this->variables[$name] = $value;
	}

	/**
	 * Set multiple variables to the scope using an array as parameter.
	 *
	 * @param array<string, Value> $pairs
	 */
	public function setVariables(array $pairs) {

		foreach ($pairs as $name => $value) {

			if (!$value instanceof Value) {
				$value = Value::buildAutomatic($value);
			}

			$this->setVariable($name, $value);

		}

	}

	public function fetchVariable(string $name): ?Value {
		return $this->variables[$name] ?? null;
	}

	public function fetchVariables(): array {
		return $this->variables;
	}

}
