<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\FuncValue;

class Context extends \Smuuf\Primi\StrictObject implements IContext {

	// use WatchLifecycle;

	const EMPTY_CONTAINER = [
		'variables' => [],
	];

	private $container = self::EMPTY_CONTAINER;

	public function reset() {
		$this->container = self::EMPTY_CONTAINER;
	}

	// Variables.

	public function setVariable(string $name, Value $value) {
		$this->container['variables'][$name] = $value;
	}

	/**
	 * Set multiple variables to the context using an array as parameter.
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

	public function getVariable(string $name): Value {

		if (!\array_key_exists($name, $this->container['variables'])) {
			throw new InternalUndefinedVariableException($name);
		}

		return $this->container['variables'][$name];

	}

	public function getVariables(): array {
		return $this->container['variables'];
	}

	// Debugging.

	public function ___debug_zvals() {

		if (extension_loaded('xdebug_debug_zval')) {
			$tmp = $this;
			xdebug_debug_zval('tmp');
		}

	}

}
