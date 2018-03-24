<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\InternalArgumentCountException;

class FuncValue extends Value {

	const TYPE = "function";

	/** @var FunctionContainer The function container itself. **/
	protected $value;

	public function __construct(FunctionContainer $fn) {
		$this->value = $fn;
	}

	public function getStringValue(): string {
		return "__function__";
	}

	public function invoke(array $args) {

		$fnArgs = $this->value->getArgs();

		if (\count($fnArgs) !== \count($args)) {
			throw new InternalArgumentCountException(
				$this->getStringValue(),
				\count($args),
				\count($fnArgs)
			);
		}

		// Simply execute the closure with passed arguments.
		return ($this->value->getClosure())(...$args);

	}

}
