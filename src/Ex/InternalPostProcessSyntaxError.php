<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

/**
 * Post-process internal syntax error exception that can be thrown by any
 * kind of node handler's "::reduce()" method.
 * @internal
 */
class InternalPostProcessSyntaxError extends EngineException {

	/** Specific reason of the syntax error, if specified. */
	private ?string $reason;

	public function __construct(?string $reason = null) {
		$this->reason = $reason;
	}

	public function getReason(): ?string {
		return $this->reason;
	}

}
