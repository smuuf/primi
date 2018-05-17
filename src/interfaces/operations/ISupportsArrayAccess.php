<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\ArrayInsertionProxy;

interface ISupportsArrayAccess {

	public function arraySet(?string $key, Value $value);
	public function arrayGet(string $key): Value;
	public function getArrayInsertionProxy(?string $key): ArrayInsertionProxy;

}

