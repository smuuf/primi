<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\InsertionProxy;

interface ISupportsInsertion {

	public function insert(?Value $key, Value $value): Value;
	public function getInsertionProxy(?Value $key): InsertionProxy;

}

