<?php

namespace Smuuf\Primi;

class InternalArgumentCountException extends InternalException {

	protected $passedCount;
	protected $expectedCount;

	public function __construct(int $passed = null, int $expected = null) {

		parent::__construct();

		$this->passedCount = $passed;
		$this->expectedCount = $expected;

	}

	public function getPassedCount(): int {
		return $this->passedCount;
	}

	public function getExpectedCount(): int {
		return $this->expectedCount;
	}

}
