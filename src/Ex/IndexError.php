<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

class IndexError extends LookupError {

	public function __construct(int $index) {
		parent::__construct("Undefined index $index");
	}

}
