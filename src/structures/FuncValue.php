<?php

namespace Smuuf\Primi\Structures;

class FuncValue extends Value {

	const TYPE = "function";

	/** @var FnContainer The function container itself. **/
	protected $value;

	/** @var Value Value object acting as "this" when this function is invoked. **/
	protected $self;

	public function __construct(FnContainer $fn) {
		$this->value = $fn;
	}

	public function getStringValue(): string {
		return "__function__";
	}

	/**
	 * Set a value object as "self" to this functions. Ie. bind together the
	 * passed parent value and this function value. When this function value is
	 * later invoked, the bound "self" is then passed as the first argument into
	 * this function value's inner closure.
	 */
	public function bind(Value $self) {
		$this->self = $self;
	}

	public function getBoundValue() {
		return $this->self;
	}

	public function invoke(array $args = []) {

		$closure = ($this->value->getClosure());

		// If this function has "self" specified, pass it as the first argument.
		if ($this->self) {
			array_unshift($args, $this->self);
		}

		// Simply execute the closure with passed arguments.
		return $closure(...$args);

	}

}
