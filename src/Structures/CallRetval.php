<?php

declare(strict_types=1);

namespace Smuuf\Primi\Structures;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Interned;

/**
 * @internal
 */
class CallRetval {

	use StrictObject;

	private AbstractValue $value;

	public function __construct(?AbstractValue $value = \null) {
		$this->value = $value ?? Interned::null();
	}

	public function getValue(): AbstractValue {
		return $this->value;
	}

}

