<?php

namespace Smuuf\Primi;

class InternalArgumentTypeErrorException extends InternalTypeErrorException {

	protected $index = null;

	public function __construct(int $index, string $passed, array $expected) {

		parent::__construct($passed, $expected);
		$this->index = $index;

	}

	public function __toString(): string {

		// Convert Primi value classes names to Primi type names.
		$expectedNames = array_map(function($class) {
			return $class::TYPE;
		}, $this->expected);

		// The value did not match any of the types provided.
		return sprintf(
			"Parameter {$this->index} must be '%s' but got '%s'",
			implode("|", $expectedNames),
			$this->passed
		);

	}

}
