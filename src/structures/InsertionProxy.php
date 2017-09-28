<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\ISupportsInsertion;

class InsertionProxy extends \Smuuf\Primi\Object {

	protected $target;
	protected $key;

	public function __construct(ISupportsInsertion $target, string $key) {
		$this->target = $target;
		$this->key = $key;
	}

	public function commit(Value $value) {
		$this->target->insert($this->key, $value);
	}

}
