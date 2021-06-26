<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

class ModuleNotFoundError extends ImportError {

	public function __construct(string $symbol) {
		parent::__construct("Module '{$symbol}' not found");
	}

}
