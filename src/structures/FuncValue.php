<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Stats;

/**
 * @property FnContainer $value Internal map container.
 */
class FuncValue extends Value {

	const TYPE = "function";

	public function __construct(FnContainer $fn) {
		$this->value = $fn;
		Stats::add('value_count_func');
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
