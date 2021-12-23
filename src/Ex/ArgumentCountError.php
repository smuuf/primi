<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

class ArgumentCountError extends RuntimeError {

	protected int $passed;
	protected int $expected;

	public function __construct(
		int $passed,
		int $expected
	) {

		$this->passed = $passed;
		$this->expected = $expected;

		parent::__construct($this->buildMessage());

	}

	private function buildMessage(): string {

		// The value did not match any of the types provided.
		return \sprintf(
			"Expected %s arguments but got %s",
			$this->expected,
			$this->passed
		);

	}

}
