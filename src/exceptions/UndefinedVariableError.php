<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

class UndefinedVariableError extends LookupError {

	public function __construct(string $msg, $line = \false, $pos = \false) {
		parent::__construct("Undefined variable '$msg'", $line, $pos);
	}

}
