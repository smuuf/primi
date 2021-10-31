<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

use \Smuuf\Primi\Values\AbstractValue;

class BinaryOperationError extends RuntimeError {

	public function __construct(
		string $op,
		AbstractValue $left,
		AbstractValue $right
	) {

		$msg = \sprintf(
			"Cannot use operator '%s' with '%s' and '%s'",
			$op, $left->getTypeName(), $right->getTypeName()
		);

		parent::__construct($msg);

	}

}
