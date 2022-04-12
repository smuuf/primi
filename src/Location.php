<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use \Smuuf\StrictObject;

class Location {

	use StrictObject;

	/** Name of the location (e.g. module name or source ID). */
	private string $name;

	/** Line in the file. */
	private int $line;

	/** Position on the line, if specified. */
	private ?int $position = \null;

	public function __construct(
		string $name,
		int $line,
		?int $position = \null
	) {

		$this->name = $name;
		$this->line = $line;
		$this->position = $position;

	}

	public function __toString(): string{

		$posInfo = $this->position !== \null
			? " at position {$this->position}"
			: '';

		return "{$this->name} on line {$this->line}$posInfo";

	}

	/**
	 * Get human-friendly name of the location.
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * Get line in the source file.
	 */
	public function getLine(): int {
		return $this->line;
	}

	/**
	 * Position on the line, if specified.
	 */
	public function getPosition(): ?int {
		return $this->position;
	}

}
