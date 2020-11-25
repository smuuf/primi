<?php

namespace Smuuf\Primi\Structures;

/**
 * @property FnContainer $value Internal map container.
 */
class FuncValue extends Value {

	const TYPE = "function";

	public function __construct(FnContainer $fn) {
		$this->value = $fn;
	}

	public function isTruthy(): bool {
		return \true;
	}

	public function getStringRepr(): string {
		return \sprintf(
			"<function: %s>",
			$this->value->isPhpFunction() ? 'native' : 'user'
		);
	}

	public function invoke(
		Context $context,
		array $args = []
	): ?Value {

		// Simply call the closure with passed arguments and other info.
		return $this->value->callClosure($context, $args);

	}

}
