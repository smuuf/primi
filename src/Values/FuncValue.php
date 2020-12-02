<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\Stats;
use \Smuuf\Primi\Structures\FnContainer;

/**
 * @property FnContainer $value Internal map container.
 */
class FuncValue extends AbstractValue {

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
	): ?AbstractValue {

		// Simply call the closure with passed arguments and other info.
		return $this->value->callClosure($context, $args);

	}

}
