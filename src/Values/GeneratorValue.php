<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Stdlib\StaticTypes;

class GeneratorValue extends AbstractNativeValue {

	protected const TYPE = "Generator";

	public function __construct(iterable $it) {
		$this->value = $it;
	}

	public function getType(): TypeValue {
		return StaticTypes::getGeneratorType();
	}

	public function getStringRepr(): string {
		return "<generator>";
	}

	public function getIterator(): \Iterator {

		foreach ($this->value as $item) {
			yield $item;
		}

	}

}
