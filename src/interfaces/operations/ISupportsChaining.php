<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;

interface ISupportsChaining {

	public function chain(Value $keyValue);

}
