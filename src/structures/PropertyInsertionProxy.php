<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\ErrorException;

class PropertyInsertionProxy extends InsertionProxy {

	public function commit(Value $value) {

		try {
			$this->target->propertySet($this->key, $value);
		} catch (\TypeError $e) {
			throw new ErrorException(sprintf(
				"Cannot insert type '%s' into type '%s'",
				$value::TYPE,
				$this->target::TYPE
			));
		}

	}

}
