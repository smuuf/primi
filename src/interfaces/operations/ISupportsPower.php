<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;

interface ISupportsPower {

	public function doPower(Value $operand): ?Value;

}
