<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Stdlib\StaticTypes;
use \Smuuf\Primi\Helpers\Interned;

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

		foreach ($this->value as $index => $item) {
			yield Interned::number((string) $index) => $item;
		}

	}

}
