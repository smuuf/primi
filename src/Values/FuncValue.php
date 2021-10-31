<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Location;
use \Smuuf\Primi\Stdlib\StaticTypes;
use \Smuuf\Primi\Structures\FnContainer;

/**
 * @property FnContainer $value Internal map container.
 */
class FuncValue extends AbstractNativeValue {

	protected const TYPE = "Function";
	private array $partialArgs = [];

	public function __construct(FnContainer $fn, array $partialArgs = []) {
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
		array $args = [],
		?Location $callsite = \null
	): ?AbstractValue {

		// Simply call the closure with passed arguments and other info.
		return $this->value->callClosure(
			$context,
			[...$this->partialArgs, ...$args],
			$callsite
		);

	}

}
