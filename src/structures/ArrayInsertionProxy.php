<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\ErrorException;

class ArrayInsertionProxy extends InsertionProxy {

	public function commit(Value $value) {

		try {
			$this->target->arraySet($this->key, $value);
		} catch (\TypeError $e) {
			$msg = sprintf("Cannot insert '%s' into '%s'", $value::TYPE, $this->target::TYPE);
			throw new ErrorException($msg);
		}

	}

}
