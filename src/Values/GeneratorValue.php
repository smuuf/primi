<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Stdlib\StaticTypes;

class GeneratorValue extends AbstractNativeValue {

	public const TYPE = "Generator";

	/**
	 * @param iterable<AbstractValue> $it
	 */
	public function __construct(iterable $it) {
		$this->value = $it;
	}

	public function getType(): TypeValue {
		return StaticTypes::getGeneratorType();
	}

	public function getStringRepr(): string {
		return "<generator>";
	}

	/**
	 * @return \Iterator<int, AbstractValue>
	 */
	public function getIterator(): \Iterator {
		if ($this->value->valid()) {
			yield from $this->value;
		}
	}

}
