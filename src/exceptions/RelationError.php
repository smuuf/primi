<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

use \Smuuf\Primi\Structures\Value;

class RelationError extends RuntimeError {

	public function __construct(string $op, Value $left, Value $right) {

		$lType = $left::TYPE;
		$rType = $right::TYPE;
		$msg = "Undefined relation '$op' between '{$lType}' and '{$rType}'";

		parent::__construct($msg);

	}

}
