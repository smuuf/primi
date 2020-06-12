<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\InsertionProxy;

interface ISupportsKeyAccess {

	public function arraySet(?string $key, Value $value);
	public function arrayGet(string $key): Value;
	public function getInsertionProxy(?string $key): InsertionProxy;

}

