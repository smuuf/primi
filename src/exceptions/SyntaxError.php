<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

class SyntaxError extends BaseError {

	public function __construct(int $line, int $position, string $excerpt = '') {

		$near = $excerpt
			? sprintf("near '%s' ", trim($excerpt))
			: '';

		$msg = \sprintf(
			"Syntax error %s@ line %s, position %s",
			$near,
			$line,
			$position
		);

		parent::__construct($msg);

	}

}
