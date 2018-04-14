<?php

namespace Smuuf\Primi;

class InternalArgumentCountException extends InternalException {

	public function __construct(string $name, int $passed = null, int $expected = null) {
		$this->passedCount = $passedCount;
		$this->expectedCount = $expectedCount;
	}

	public function getPassedCount() {
		$this->passedCount;
	}

	public function getExpectedCount() {
		$this->expectedCount;
	}

}
