<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\PropertyInsertionProxy;

interface ISupportsPropertyAccess {

	public function propertySet(string $key, Value $value);
	public function propertyGet(string $key): Value;
	public function getPropertyInsertionProxy(string $key): PropertyInsertionProxy;

}

