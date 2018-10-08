<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\StrictObject;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\FuncValue;

class Context extends StrictObject implements IContext {

	// use WatchLifecycle;

	private static $globals = [];
	private $vars = [];

	public function reset(bool $wipeGlobals = false) {

		if ($wipeGlobals) {
			self::$globals = [];
		}

		$this->vars = [];

	}

	// Variables.

	public function setVariable(
		string $name,
		Value $value,
		bool $global = false
	) {

		if ($global) {
			self::$globals[$name] = $value;
		} else {
			$this->vars[$name] = $value;
		}

	}

	/**
	 * Set multiple variables to the context using an array as parameter.
	 *
	 * @param array<string, Value> $pairs
	 */
	public function setVariables(array $pairs, bool $global = false) {

		foreach ($pairs as $name => $value) {

			if (!$value instanceof Value) {
				$value = Value::buildAutomatic($value);
			}

			$this->setVariable($name, $value, $global);

		}

	}

	public function getVariable(string $name): Value {

		// Variables of current context instance have higher priority than
		// global variables.
		if (isset($this->vars[$name])) {
			return $this->vars[$name];
		}

		if (isset(self::$globals[$name])) {
			return self::$globals[$name];
		}

		throw new InternalUndefinedVariableException($name);

	}

	public function getVariables(): array {
		return $this->vars;
	}

	// Debugging.

	public function ___debug_zvals() {

		if (extension_loaded('xdebug_debug_zval')) {
			$tmp = $this;
			xdebug_debug_zval('tmp');
		}

	}

}
