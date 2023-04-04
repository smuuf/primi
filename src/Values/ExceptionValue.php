<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use Smuuf\Primi\Context;
use Smuuf\Primi\MagicStrings;
use Smuuf\Primi\Ex\EngineError;
use Smuuf\Primi\Helpers\Interned;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Helpers\Types;

class ExceptionValue extends AbstractBuiltinValue {

	public const TYPE = "Exception";

	/**
	 * @param TypeValue $excType A type object inheriting from BaseException
	 *     type.
	 * @param list<AbstractValue> $args Arbitrary arguments passed to
	 *     the exception.
	 */
	public function __construct(
		private TypeValue $excType,
		private Context $ctx,
		array $args,
	) {

		if (!Types::isSubtypeOf($excType, StaticExceptionTypes::getBaseExceptionType())) {
			throw new EngineError(sprintf(
				"Type object passed to %s must inherit from BaseException",
				__METHOD__,
			));
		}

		$this->attrs['args'] = new TupleValue($args);

	}

	public function getType(): TypeValue {
		return $this->excType;
	}

	public function getTypeName(): string {
		return $this->excType->getName();
	}

	public function getArgs(): TupleValue {
		return $this->attrs['args'];
	}

	public function isTruthy(): bool {
		return true;
	}

	public function getStringRepr(): string {

		if ($magic = $this->attrGet(MagicStrings::MAGICMETHOD_REPR)) {
			$result = $magic->invoke($this->ctx);
			return $result->getStringValue();
		}

		// Build repr like "SomeExceptionType('first arg', 2, false)".
		$argsRepr = $this->attrs['args']->getStringRepr();
		$excTypeName = $this->excType->getName();
		return "$excTypeName{$argsRepr}";

	}

	public function getStringValue(): string {

		if ($magic = $this->attrGet(MagicStrings::MAGICMETHOD_STRING)) {
			$result = $magic->invoke($this->ctx);
			return $result->getStringValue();
		}

		// Build string like "SomeExceptionType: the only arg" if there's only a
		// single argument, or "SomeExceptionType: ('first arg', 2, false)" if
		// there are more arguments.
		$args = $this->attrs['args'];
		if ($args->getLength() === 0) {
			$argsRepr = '';
		} elseif ($args->getLength() === 1) {
			$argsRepr = $args->itemGet(Interned::number('0'))->getStringValue();
		} else {
			$argsRepr = $args->getStringValue();
		}

		$argsRepr  = $argsRepr ? ": $argsRepr" : "";
		$excTypeName = $this->excType->getName();
		return "$excTypeName{$argsRepr}";

	}

}
