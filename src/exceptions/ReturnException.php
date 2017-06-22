<?php

namespace Smuuf\Primi;

class ReturnException extends \RuntimeException {

	/** @var mixed **/
	protected $return;

	public function __construct($value) {
		$this->value = $value;
	}

	public function getValue() {
		return $this->value;
	}

}
