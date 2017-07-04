<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\Func;

class Context extends \Smuuf\Primi\Object {

	const EMPTY_CONTAINER = [
		'variables' => [],
		'functions' => [],
	];

	private $container = self::EMPTY_CONTAINER;

	public function reset() {
		$this->container = self::EMPTY_CONTAINER;
	}

	// Variables.

	public function setVariable(string $name, Value $value) {
		$this->container['variables'][$name] = $value;
	}

	public function setVariables(array $pairs) {
		$this->container['variables'] = array_merge($this->container['variables'], $pairs);
	}

	public function getVariable(string $name) {

		if (!array_key_exists($name, $this->container['variables'])) {
			throw new UndefinedVariableException($name);
		}

		return $this->container['variables'][$name];

	}

	public function getVariables() {
		return $this->container['variables'];
	}

	// Functions.

	public function setFunction(string $name, Func $function) {
		$this->container['functions'][$name] = $function;
	}

	public function setFunctions(array $pairs) {
		$this->container['functions'] = array_merge($this->container['functions'], $pairs);
	}

	public function getFunction(string $name) {
		return $this->container['functions'][$name] ?? null;
	}

	public function getFunctions() {
		return $this->container['functions'];
	}

}
