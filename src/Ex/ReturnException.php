<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

use \Smuuf\Primi\Helpers\Interned;

class ReturnException extends ControlFlowException {

	public const ID = 'return';

	/** @var mixed */
	protected $value;

	public function __construct($value = \null) {
		parent::__construct();
		$this->value = $value;
	}

	public function getValue() {
		return $this->value ?? Interned::null();
	}

}
