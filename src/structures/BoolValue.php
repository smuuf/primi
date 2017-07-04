<?php

namespace Smuuf\Primi\Structures;

class BoolValue extends Value {

	const TYPE = "bool";

	public function __construct(bool $value) {
		$this->value = $value;
	}

}
