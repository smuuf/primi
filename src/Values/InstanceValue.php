<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\MagicStrings;
use \Smuuf\Primi\Values\TypeValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Structures\CallArgs;

/**
 * Class for representing instances of userland classes/types.
 */
class InstanceValue extends AbstractValue {

	protected Context $ctx;
	protected TypeValue $type;

	public function __construct(TypeValue $type, Context $ctx) {
		$this->type = $type;
		$this->ctx = $ctx;
	}

	public function getStringRepr(): string {

		if ($magic = $this->attrGet(MagicStrings::MAGICMETHOD_REPR)) {

			$result = $magic->invoke($this->ctx);
			return $result->getStringValue();

		}

		$id = Func::object_hash($this);
		return "<instance '{$this->type->getName()}' {$id}>";

	}

	public function getType(): TypeValue {
		return $this->type;
	}

	public function getTypeName(): string {
		return $this->type->getName();
	}

	public function attrSet(string $key, AbstractValue $value): bool {
		$this->attrs[$key] = $value;
		return \true;
	}

	public function isEqualTo(
		AbstractValue $right
	): ?bool {

		if ($magic = $this->attrGet(MagicStrings::MAGICMETHOD_OP_EQ)) {

			$result = $magic->invoke($this->ctx, new CallArgs([$right]));
			if ($result === Interned::constNotImplemented()) {
				return null;
			}

			return $result->isTruthy();

		}

		return parent::isEqualTo($right);

	}

	public function doAddition(AbstractValue $right): ?AbstractValue {

		if ($magic = $this->attrGet(MagicStrings::MAGICMETHOD_OP_ADD)) {

			$result = $magic->invoke($this->ctx, new CallArgs([$right]));
			if ($result === Interned::constNotImplemented()) {
				return null;
			}

			return $result;

		}

		return parent::isEqualTo($right);

	}

	public function doSubtraction(AbstractValue $right): ?AbstractValue {

		if ($magic = $this->attrGet(MagicStrings::MAGICMETHOD_OP_SUB)) {

			$result = $magic->invoke($this->ctx, new CallArgs([$right]));
			if ($result === Interned::constNotImplemented()) {
				return null;
			}

			return $result;

		}

		return parent::isEqualTo($right);

	}

	public function dirItems(): ?array {

		return \array_merge(
			\array_keys($this->attrs),
			$this->getType()->dirItems()
		);

	}

}
