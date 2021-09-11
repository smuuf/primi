<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

class VariableImportError extends ImportError {

	public function __construct(string $symbol, string $dotpath) {
		parent::__construct("Variable '{$symbol}' not found in module '{$dotpath}'");
	}

}
