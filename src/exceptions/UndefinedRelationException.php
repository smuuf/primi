<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;

class UndefinedRelationException extends ErrorException {

	public function __construct(string $op, Value $left, Value $right) {

		$lType = $left::TYPE;
		$rType = $right::TYPE;
		$msg = "Undefined '$op' relation of '{$lType}' and '{$rType}'";

		parent::__construct($msg);

	}

}
