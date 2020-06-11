<?php

namespace Smuuf\Primi;

class InternalTypeErrorException extends InternalException {

	protected $passed = null;
	protected $expected = [];

	public function __construct(string $passed, array $expected = []) {

		parent::__construct();

		$this->passed = $passed;
		$this->expected = $expected;

	}

	public function __toString(): string {

		if (!$this->expected) {
			return sprintf("Got unexpected type '%s'", $this->passed);
		}

		// Convert Primi value classes names to Primi type names.
		$expectedNames = array_map(function($class) {
			return $class::TYPE;
		}, $this->expected);

		// The value did not match any of the types provided.
		return sprintf(
			"Expected type '%s' but got '%s'",
			implode("|", $expectedNames),
			$this->passed
		);

	}

}
