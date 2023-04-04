<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

/**
 * Internal engine exception thrown when
 */
class UnhashableTypeException extends EngineException {

	public function __construct(
		public readonly string $type,
	) {}

}
