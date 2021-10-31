<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Location;
use \Smuuf\Primi\Stdlib\StaticTypes;
use \Smuuf\Primi\Helpers\Stats;
use \Smuuf\Primi\Structures\FnContainer;

/**
 * @property FnContainer $value Internal map container.
 */
class FuncValue extends AbstractNativeValue {

	protected const TYPE = "Function";

	public function __construct(FnContainer $fn) {
		Stats::add('values_func');
		$this->value = $fn;
	}

	public function getType(): TypeValue {
		return StaticTypes::getFunctionType();
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
		array $args = [],
		?Location $callsite = null
	): ?AbstractValue {

		// Simply call the closure with passed arguments and other info.
		return $this->value->callClosure($context, $args, $callsite);

	}

}
