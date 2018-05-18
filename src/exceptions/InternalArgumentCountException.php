<?php

namespace Smuuf\Primi;

class InternalArgumentCountException extends InternalException {

	public function __construct(int $passed = null, int $expected = null) {
		$this->passedCount = $passed;
		$this->expectedCount = $expected;
	}

	public function getPassedCount() {
		$this->passedCount;
	}

	public function getExpectedCount() {
		$this->expectedCount;
	}

}
