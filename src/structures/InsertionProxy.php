<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\ISupportsInsertion;

/**
 * Insertion proxy is a special structure that encapsulates a value object which
 * supports insertion. This proxy is used in situations when we already know
 * the KEY under which we want to store something into encapsulated value, but
 * at the same time we don't yet know what VALUE will be stored.
 * At that point an insertion proxy is created with KEY being set and
 * VALUE can be "commited" later.
 *
 * @see \Smuuf\Primi\Handlers\VariableVector
 */
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
