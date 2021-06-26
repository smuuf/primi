<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

use Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Location;

/**
 * All errors that Primi knows will extend this base error exception class.
 * When thrown, information about location of the error must be known and
 * passed into this exception object.
 */
class ErrorException extends BaseException {

	/**
	 * Location object representing location of the error.
	 */
	private Location $location;

	/**
	 * Traceback (which is really just the callstack).
	 * @var array<array{string, Location}>
	 */
	private ?array $traceback = \null;

	/**
	 * @param array<array{string, Location}> $traceback
	 */
	public function __construct(
		string $msg,
		Location $location,
		?array $traceback = \null
	) {

		$loc = \sprintf(
			"@ %s on line %d, position %d",
			$location->getModule(),
			$location->getLine(),
			$location->getPosition(),
		);

		$tb = $traceback
			? Func::get_traceback_as_string($traceback)
			: '';

		parent::__construct("$msg $loc\n$tb");
		$this->location = $location;
		$this->traceback = $traceback;

	}

	/** Get object representing location of the error. */
	public function getLocation(): Location {
		return $this->location;
	}

	/**
	 * Traceback, if available.
	 * @var array<array{string, Location}>
	 */
	public function getTraceback(): ?array {
		return $this->traceback;
	}

}

