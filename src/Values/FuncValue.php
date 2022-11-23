<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Location;
use \Smuuf\Primi\MagicStrings;
use \Smuuf\Primi\Stdlib\StaticTypes;
use \Smuuf\Primi\Structures\CallArgs;
use \Smuuf\Primi\Structures\FnContainer;

/**
 * @property FnContainer $value Internal map container.
 */
class FuncValue extends AbstractNativeValue {

	public const TYPE = "func";
	private string $name;

	public function __construct(
		FnContainer $fn,
		protected ?CallArgs $partialArgs = \null,
		protected bool $isStatic = \false,
	) {

		$this->value = $fn;

		$this->name = $fn->getName();
		$this->attrs[MagicStrings::ATTR_NAME] = Interned::string($this->name);

	}

	/**
	 * Get full name of the function.
	 */
	public function isStatic(): bool {
		return $this->isStatic;
	}

	public function getType(): TypeValue {
		return StaticTypes::getFuncType();
	}

	/**
	 * Get full name of the function.
	 */
	public function getName(): ?string {
		return $this->name;
	}

	public function isTruthy(): bool {
		return \true;
	}

	public function getStringRepr(): string {

		return \sprintf(
			"<%s%s%s>",
			$this->value->isPhpFunction() ? 'native ' : '',
			$this->partialArgs ? 'partial function' : 'function',
			$this->name ? ": {$this->name}" : '',
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
