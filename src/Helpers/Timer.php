<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\Helpers\Traits\StrictObject;

class Timer {

	use StrictObject;

	/** @var float|null Point in time where timer started. */
	private $time = \null;

	/**
	 * Start the timer (set a point in time to measure from).
	 */
	public function start(): self {

		$this->time = Func::monotime();
		return $this;

	}

	/**
	 * Get current duration from the timer start.
	 */
	public function get(): float {

		if ($this->time === \null) {
			throw new \LogicException("Timer hasn't been started");
		}

		return Func::monotime() - $this->time;

	}

}
