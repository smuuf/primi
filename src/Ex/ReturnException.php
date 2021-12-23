<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Values\AbstractValue;

class ReturnException extends ControlFlowException {

	public const ID = 'return';

	/** @var AbstractValue|null */
	protected $value;

	/**
	 * @param mixed $value
	 */
	public function __construct($value = \null) {
		parent::__construct();
		$this->value = $value;
	}

	/**
	 * @return AbstractValue
	 */
	public function getValue() {
		return $this->value ?? Interned::null();
	}

}
