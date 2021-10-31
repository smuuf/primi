<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

use \Smuuf\Primi\Values\AbstractValue;

class RelationError extends RuntimeError {

	public function __construct(string $op, AbstractValue $left, AbstractValue $right) {

		$lType = $left->getTypeName();
		$rType = $right->getTypeName();
		$msg = "Undefined relation '$op' between '{$lType}' and '{$rType}'";

		parent::__construct($msg);

	}

}
