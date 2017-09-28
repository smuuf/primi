<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\InsertionProxy;

interface ISupportsInsertion {

	public function insert(string $key, Value $value);
	public function getInsertionProxy(string $key): InsertionProxy;

}

