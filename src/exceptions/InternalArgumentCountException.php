<?php

namespace Smuuf\Primi;

class InternalArgumentCountException extends InternalException {

	public function __construct(string $name, int $passed = null, int $expected = null) {

		$counts = null;
		if ($expected) {
			$counts = sprintf(" (%d instead of %d)", $passed, $expected);
		}

		$msg = sprintf(
			"Too few arguments passed to '%s'%s.",
			$name,
			$counts
		);

		parent::__construct($msg);

	}

}
