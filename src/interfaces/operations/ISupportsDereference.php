<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;

interface ISupportsDereference {

	public function dereference(Value $keyValue);

}
