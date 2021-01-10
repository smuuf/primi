<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

class CircularImportError extends ImportError {

	/**
	 * @param array<string> $importStack List representing current import stack.
	 */
	public function __construct(string $nextModule) {
		parent::__construct("Circular import when importing: {$nextModule}");
	}

}
