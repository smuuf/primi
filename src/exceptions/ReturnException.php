<?php

namespace Smuuf\Primi;

class ReturnException extends InternalException {

	/** @var mixed **/
	protected $value;

	public function __construct($value) {
		parent::__construct();
		$this->value = $value;
	}

	public function getValue() {
		return $this->value;
	}

}
