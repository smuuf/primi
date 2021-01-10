<?php

declare(strict_types=1);

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Traits\StrictObject;

/**
 * @internal
 * @see InsertionProxyInterface
 */
class AttrInsertionProxy implements InsertionProxyInterface {

	use StrictObject;

	protected $target;
	protected $key;

	public function __construct(
		?AbstractValue $key,
		AbstractValue $target
	) {
		$this->target = $target;
		$this->key = $key;
	}

	public function commit(AbstractValue $value): void {

		$success = $this->target->attrSet($this->key, $value);
		if ($success === \false) {
			throw new TypeError(sprintf(
				"Type '%s' does not support attribute assignment.",
				($this->target)::TYPE
			));
		}

	}

}
