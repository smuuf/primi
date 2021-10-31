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

	protected AbstractValue $target;
	protected ?AbstractValue $key;

	public function __construct(
		?AbstractValue $key,
		AbstractValue $target
	) {
		$this->target = $target;
		$this->key = $key;
	}

	public function commit(AbstractValue $value): void {

		$success = $this->target->itemSet($this->key, $value);
		if ($success === \false) {
			throw new TypeError(sprintf(
				"Type '%s' does not support item assignment",
				($this->target)->getTypeName()
			));
		}

	}

}
