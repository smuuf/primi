<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

use \Smuuf\Primi\Location;
use \Smuuf\Primi\StackFrame;
use \Smuuf\Primi\Helpers\Func;

/**
 * All errors that Primi knows will extend this base error exception class.
 * When thrown, information about location of the error must be known and
 * passed into this exception object.
 */
class ErrorException extends BaseException {

	/** Location object representing location of the error. */
	private Location $location;

	/**
	 * Traceback (which is really just the callstack).
	 *
	 * @var ?array<StackFrame>
	 */
	private ?array $traceback = \null;

	/**
	 * @param ?array<StackFrame> $traceback
	 */
	public function __construct(
		string $msg,
		Location $location,
		?array $traceback = \null
	) {

		$loc = \sprintf(
			"@ %s on line %d",
			$location->getName(),
			$location->getLine()
		);

		$tb = $traceback
			? "\n" . Func::get_traceback_as_string($traceback)
			: '';

		parent::__construct("$msg {$loc}{$tb}");
		$this->location = $location;
		$this->traceback = $traceback;

	}

	/** Get object representing location of the error. */
	public function getLocation(): Location {
		return $this->location;
	}

	/**
	 * Traceback, if there's any.
	 *
	 * @return ?array<StackFrame>
	 */
	public function getTraceback(): ?array {
		return $this->traceback;
	}

}

