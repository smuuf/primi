<?php

declare(strict_types=1);

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\StrictObject;

/**
 * @internal
 * @see InsertionProxyInterface
 */
class ItemInsertionProxy implements InsertionProxyInterface {

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
		//xdebug_break();
		$success = $this->target->itemSet($this->key, $value);
		if ($success === \false) {
			throw new TypeError(sprintf(
				"Type '%s' does not support item assignment",
				($this->target)::TYPE
			));
		}

	}

}
