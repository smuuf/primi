<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

use \Smuuf\Primi\Structures\Value;

class BinaryOperationError extends RuntimeError {

	public function __construct(
		string $op,
		Value $left,
		Value $right
	) {

		$msg = \sprintf(
			"Cannot use operator '%s' with '%s' and '%s'",
			$op, ($left)::TYPE, ($right)::TYPE
		);

		parent::__construct($msg);

	}

}
