<?php

namespace Smuuf\Primi\Structures;

class ArrayInsertionProxy extends InsertionProxy {

	public function commit(Value $value) {
		$this->target->arraySet($this->key, $value);
	}

}
