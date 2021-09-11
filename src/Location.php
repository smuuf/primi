<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use \Smuuf\StrictObject;

class Location {

	use StrictObject;

	/** Name of the name (file). */
	private string $name;

	/** Line in the name file. */
	private int $line;

	public function __construct(string $name, int $line) {

		$this->name = $name;
		$this->line = $line;

	}

	public function __toString(): string{
		return "{$this->name} on line {$this->line}";
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

}
