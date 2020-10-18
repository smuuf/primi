<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\StrictObject;
use \Smuuf\Primi\Structures\Value;

class Context extends StrictObject implements IContext {

	// use WatchLifecycle;

	private $globals = [];
	private $vars = [];

	/** @var Interpreter|null Intepreter object using this context. */
	private $interpreter;

	/**
	 * Inject the `Interpreter` instance into this `Context`.
	 */
	public function setInterpreter(Interpreter $i) {
		$this->interpreter = $i;
	}

	/**
	 * Return the `Interpreter` instance using this `Context`.
	 */
	public function getInterpreter(): Interpreter {
		return $this->interpreter;
	}

	public function reset(bool $wipeGlobals = false): void {

		if ($wipeGlobals) {
			$this->globals = [];
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
			$this->globals[$name] = $value;
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

	public function getVariable(string $name): ?Value {

		// Variables of current context instance have higher priority than
		// global variables.
		if (isset($this->vars[$name])) {
			return $this->vars[$name];
		}

		if (isset($this->globals[$name])) {
			return $this->globals[$name];
		}

		// This should be slightly faster than throwing exceptions for undefined
		// variables.
		return null;

	}

	public function getVariables(bool $includeGlobals = false): array {
		return $this->vars + ($includeGlobals ? $this->globals : []);
	}

	// Debugging.

	public function ___debug_zvals() {

		if (extension_loaded('xdebug_debug_zval')) {
			$tmp = $this;
			xdebug_debug_zval('tmp');
		}

	}

}
