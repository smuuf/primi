<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

class TypeError extends RuntimeError {

	protected $passed = \null;
	protected $expected = [];
	protected $extraMessage = '';

	public function __construct(
		string $passed,
		array $expected = [],
		string $extraMessage = ''
	) {

		$this->passed = $passed;
		$this->expected = $expected;
		$this->extraMessage = $extraMessage ? " $extraMessage" : '';

		parent::__construct($this->buildMessage());

	}

	private function buildMessage(): string {

		if (!$this->expected) {
			return \sprintf(
				"Got unexpected %s%s",
				$this->passed,
				$this->extraMessage
			);
		}

		// Convert Primi value classes names to Primi type names.
		$expectedNames = \array_map(function($class) {
			return $class::TYPE;
		}, $this->expected);

		// The value did not match any of the types provided.
		return \sprintf(
			"Expected %s but got %s%s",
			\implode("|", $expectedNames),
			$this->passed,
			$this->extraMessage
		);

	}

}
