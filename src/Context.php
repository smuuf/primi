<?php

namespace Smuuf\Primi;

class Context extends \Smuuf\Primi\Object {

	private $container = [
		'variables' => [],
	];

	public function setVariable(string $name, $value) {
		$this->container['variables'][$name] = $value;
	}

	public function setVariables(array $pairs) {
		$this->container['variables'] = $pairs;
	}

	public function getVariable(string $name) {
		return $this->container['variables'][$name] ?? null;
	}

	public function getVariables() {
		return $this->container['variables'];
	}

}