<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

class KeyError extends LookupError {

	public function __construct(string $key) {
		parent::__construct("Undefined key '$key'");
	}

}
