<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Stdlib\BuiltinTypes;
use \Smuuf\Primi\Structures\CallArgs;
use \Smuuf\Primi\Structures\FnContainer;

/**
 * NOTE: Although this PHP class extends from `FuncValue`, in Primi runtime
 * the `MethodType` doesn't extend `FuncType` but actually extends `object`
 * directly.
 *
 * @internal
 * @property FnContainer $value Internal map container.
 */
class MethodValue extends FuncValue {

	public const TYPE = "method";

	public function __construct(
		FnContainer $fn,
		private bool $isStatic = false,
		?AbstractValue $bind = \null,
	) {

		if ($isStatic && $bind) {
			throw new EngineError("Cannot create bound static method");
		}

		parent::__construct(
			$fn,
			$bind ? new CallArgs([$bind]) : null,
		);

	}

	public function withBind(AbstractValue $bind): self {

		if ($this->isStatic) {
			return $this;
		}

		return new MethodValue($this->value, false, $bind);

	}

	public function getType(): TypeValue {
		return BuiltinTypes::getMethodType();
	}

	public function getStringRepr(): string {

		$adjectives = \array_filter([
			$this->value->isPhpFunction() ? 'native' : '',
			$this->isStatic ? 'static' : '',
			$this->partialArgs ? 'bound' : '',
		]);

		return \sprintf(
			"<%s%s%s>",
			\implode(' ', \array_filter($adjectives)) . ($adjectives ? ' ' : ''),
			'method',
			$this->name ? ": {$this->name}" : '',
		);

	}

}
