<?php

namespace Smuuf\Primi;

class UndefinedVariableException extends ErrorException {

	public function __construct($name) {
		parent::__construct("Undefined variable '$name'");
	}

}
