<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use \Smuuf\StrictObject;

class CallFrame {

	use StrictObject;

	/** Name of the call (often function name). */
	private string $name;

	/** Callsite location. */
	private ?Location $location;

	public function __construct(string $name, ?Location $location = null) {
		$this->name = $name;
		$this->location = $location;
	}

	public function __toString(): string {
		$loc = $this->location ? " called from {$this->location}" : '';
		return "{$this->name}{$loc}";
	}

	/**
	 * Get name of the call.
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * Get call location.
	 */
	public function getLocation(): ?Location {
		return $this->location;
	}

}
