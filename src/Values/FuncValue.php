<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Location;
use \Smuuf\Primi\Stdlib\StaticTypes;
use \Smuuf\Primi\Structures\CallArgs;
use \Smuuf\Primi\Structures\FnContainer;

/**
 * @property FnContainer $value Internal map container.
 */
class FuncValue extends AbstractNativeValue {

	public const TYPE = "Function";
	private ?CallArgs $partialArgs = \null;

	public function __construct(
		FnContainer $fn,
		?CallArgs $partialArgs = \null
	) {
		$this->value = $fn;
		$this->partialArgs = $partialArgs;
	}

	public function getType(): TypeValue {
		return StaticTypes::getFunctionType();
	}

	public function isTruthy(): bool {
		return \true;
	}

	public function getStringRepr(): string {

		return \sprintf(
			"<%s: %s>",
			$this->partialArgs ? 'partial function' : 'function',
			$this->value->isPhpFunction() ? 'native' : 'user',
		);

	}

	public function invoke(
		Context $context,
		?CallArgs $args = \null,
		?Location $callsite = \null
	): ?AbstractValue {

		// If there are any partial args specified, create a new args object
		// combining partial args (its kwargs have lower priority) combined
		// with the currently passed args (its kwargs have higher priority).
		if ($this->partialArgs) {
			if ($args) {
				$args = $this->partialArgs->withExtra($args);
			} else {
				$args = $this->partialArgs;
			}
		}

		// Simply call the closure with passed arguments and other info.
		return $this->value->callClosure($context, $args, $callsite);

	}

}
