<?php

namespace Smuuf\Primi\Structures;

class FuncValue extends Value {

	const TYPE = "function";

	/** @var FnContainer The function container itself. **/
	protected $value;

	public function __construct(FnContainer $fn) {
		$this->value = $fn;
	}

	public function getStringValue(): string {
		return "__function__";
	}

	public function invoke(array $args = []) {

		// Simply execute the closure with passed arguments.
		return ($this->value->getClosure())(...$args);

	}

}
