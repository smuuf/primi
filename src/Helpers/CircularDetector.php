<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

class CircularDetector {

	/**
	 * How many instances of a single ID on the stack is still not considered
	 * as circular referencing?
	 *
	 * @const
	 */
	const LIMIT = 3;

	protected $stack = [];

	public function add(string $id): void {

		if (!isset($this->stack[$id])) {
			$this->stack[$id] = 0;
		}

		$this->stack[$id]++;

	}

	public function has(string $id): bool {
		return isset($this->stack[$id]) && $this->stack[$id] > self::LIMIT;
	}

}

