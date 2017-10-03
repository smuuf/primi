<?php

namespace Smuuf\Primi;

class UndefinedIndexException extends ErrorException {

	public function __construct(string $msg, $line = false, $pos = false) {
		parent::__construct("Undefined index '$msg'", $line, $pos);
	}

}
