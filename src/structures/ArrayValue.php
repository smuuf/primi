<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\ISupportsIteration;
use \Smuuf\Primi\ISupportsDereference;

class ArrayValue extends Value implements ISupportsIteration, ISupportsDereference {

	const TYPE = "array";

	public function __construct(array $arr) {
		$this->value = $arr;
	}

	public function getIterator(): \Iterator {
		return new \ArrayIterator($this->value);
	}

	public function dereference($index) {

		if (!isset($this->value[$index])) {
			throw new \Smuuf\Primi\ErrorException("Undefined index '$index'");
		}

		return $this->value[$index];

	}

}
