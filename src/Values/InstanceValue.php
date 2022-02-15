<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Values\TypeValue;

/**
 * Class for representing instances of userland classes/types.
 */
class InstanceValue extends AbstractValue {

	protected TypeValue $type;

	public function __construct(TypeValue $type) {
		$this->type = $type;
	}

	public function getStringRepr(): string {
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

	public function dirItems(): ?array {

		return \array_merge(
			\array_keys($this->attrs),
			$this->getType()->dirItems()
		);

	}

}
