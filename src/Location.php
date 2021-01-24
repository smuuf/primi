<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use \Smuuf\StrictObject;

class Location {

	use StrictObject;

	/** Name of the module (file). */
	private string $module;

	/** Line in the module file. */
	private int $line;

	/** Position on the line in the module file. */
	private int $position;

	public function __construct(string $module, int $line, int $position) {

		$this->module = $module;
		$this->line = $line;
		$this->position = $position;

	}

	public function __toString(): string{
		return "{$this->module} on line {$this->line}, position {$this->position}";
	}

	/**
	 * Get name of the module (file).
	 */
	public function getModule(): string {
		return $this->module;
	}

	/**
	 * Get line in the module file.
	 */
	public function getLine(): int {
		return $this->line;
	}

	/**
	 * Get position on the line in the module file.
	 */
	public function getPosition(): int {
		return $this->position;
	}
}
