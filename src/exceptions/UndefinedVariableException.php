<?php

namespace Smuuf\Primi;

class UndefinedVariableException extends InternalException {

	/** @var mixed **/
	protected $name;

	public function __construct($name) {
		$this->name = $name;
	}

	public function getName() {
		return $this->name;
	}

}
