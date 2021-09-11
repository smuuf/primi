<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

class CircularImportError extends ImportError {

	/**
	 * @param array<string> $nextModule Name of the module causing circular
	 * import.
	 */
	public function __construct(string $nextModule) {
		parent::__construct("Circular import when importing: {$nextModule}");
	}

}
