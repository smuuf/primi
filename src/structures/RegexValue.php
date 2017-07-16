<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\UnsupportedOperationException;
use \Smuuf\Primi\ISupportsMultiplication;
use \Smuuf\Primi\ISupportsAddition;
use \Smuuf\Primi\ISupportsIteration;

class RegexValue extends Value {

	const TYPE = "regex";

	public function __construct(string $regex) {
		$this->value = $regex . "u";
	}

}
