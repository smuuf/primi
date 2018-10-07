<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\StrictObject;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\FuncValue;

class Context extends StrictObject implements IContext {

	// use WatchLifecycle;

	const EMPTY_CONTAINER = [
		'user' => [],
		'internal' => [],
	];

	private $container = self::EMPTY_CONTAINER;

	public function reset() {
		$this->container = self::EMPTY_CONTAINER;
	}

	// Variables.

	public function setVariable(
		string $name,
		Value $value,
		bool $internal = false
	) {
		$type = $internal ? 'internal' : 'user';
		$this->container[$type][$name] = $value;
	}

	/**
	 * Set multiple variables to the context using an array as parameter.
	 *
	 * @param array<string, Value> $pairs
	 */
	public function setVariables(array $pairs, bool $internal = false) {

		foreach ($pairs as $name => $value) {

			if (!$value instanceof Value) {
				$value = Value::buildAutomatic($value);
			}

			$this->setVariable($name, $value, $internal);

		}

	}

	public function getVariable(string $name): Value {

		if (isset($this->container['user'][$name])) {
			return $this->container['user'][$name];
		}

		if (isset($this->container['internal'][$name])) {
			return $this->container['internal'][$name];
		}

		throw new InternalUndefinedVariableException($name);

	}

	public function getVariables(): array {
		return $this->container['user'];
	}

	// Debugging.

	public function ___debug_zvals() {

		if (extension_loaded('xdebug_debug_zval')) {
			$tmp = $this;
			xdebug_debug_zval('tmp');
		}

	}

}
