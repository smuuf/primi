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
class AttrInsertionProxy implements InsertionProxyInterface {

	use StrictObject;

	protected string $key;
	protected AbstractValue $target;

	public function __construct(
		string $key,
		AbstractValue $target
	) {
		$this->target = $target;
		$this->key = $key;
	}

	public function commit(AbstractValue $value): void {

		$success = $this->target->attrSet($this->key, $value);
		if ($success === \false) {
			throw new TypeError(sprintf(
				"Object of type '%s' does not support attribute assignment",
				$this->target->getTypeName()
			));
		}

	}

}
