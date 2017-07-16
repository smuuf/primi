<?php

namespace Smuuf\Primi;

class SyntaxErrorException extends ErrorException {

	public function __construct(string $msg, int $line, int $pos) {
		parent::__construct($msg, $line, $pos);
	}

}
