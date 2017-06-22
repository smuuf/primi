<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Func;

class Context extends \Smuuf\Primi\Object {

	private $container = [
		'variables' => [],
		'functions' => [],
	];

	// Variables.

	public function setVariable(string $name, $value) {
		$this->container['variables'][$name] = $value;
	}

	public function setVariables(array $pairs) {
		$this->container['variables'] = array_merge($this->container['variables'], $pairs);
	}

	public function getVariable(string $name) {
		return $this->container['variables'][$name] ?? null;
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