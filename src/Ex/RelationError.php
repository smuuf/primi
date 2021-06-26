<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

use \Smuuf\Primi\Values\AbstractValue;

class RelationError extends RuntimeError {

	public function __construct(string $op, AbstractValue $left, AbstractValue $right) {

		$lType = $left::TYPE;
		$rType = $right::TYPE;
		$msg = "Undefined relation '$op' between '{$lType}' and '{$rType}'";

		parent::__construct($msg);

	}

}
