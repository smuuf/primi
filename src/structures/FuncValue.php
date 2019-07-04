<?php

namespace Smuuf\Primi\Structures;

class FuncValue extends Value {

	const TYPE = "function";

	public function __construct(FnContainer $fn) {
		$this->value = $fn;
	}

	public function getStringValue(): string {
		return "function";
	}

	public function invoke(array $args = []) {

		// Simply execute the closure with passed arguments.
		return ($this->value->getClosure())(...$args);

	}

}
