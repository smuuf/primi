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
		return sprintf(
			"<function: %s>",
			$this->value->isPhpFunction() ? 'native' : 'user'
		);
	}

	public function invoke(array $args = []): ?Value {

		// Simply execute the closure with passed arguments.
		return ($this->value->getClosure())($args);

	}

}
