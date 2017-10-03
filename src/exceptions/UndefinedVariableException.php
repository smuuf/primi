<?php

namespace Smuuf\Primi;

class UndefinedVariableException extends ErrorException {

	public function __construct(string $msg, $line = false, $pos = false) {
		parent::__construct("Undefined variable '$msg'", $line, $pos);
	}

}
