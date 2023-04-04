<?php

declare(strict_types=1);

namespace Smuuf\Primi\Compiler\PostProcessors\Peephole;

use Smuuf\StrictObject;
use Smuuf\Primi\Compiler\Op;

class PatternRule {

	use StrictObject;

	/**
	 * @param string|list<string> $opType
	 */
	public function __construct(
		private string|array $opType = [],
		private ?\Closure $filter = null,
		private ?\Closure $onMatch = null,
		public readonly bool $many = false,
	) {

		// Instruction argument can be a single string or an array of
		// Ops (instructions to match in the op), so let's make sure
		// it's always an array; for easier matching later.
		$this->opType = (array) $this->opType;

	}

	/**
	 * @param array<mixed> $storage
	 */
	public function tryMatch(Op $op, array &$storage): bool {

		// If the instruction to match are specified, handle it.
		if ($this->opType) {
			if (!in_array($op->opType, $this->opType, true)) {
				return false;
			}
		}

		// If filter callback is specified, call it. If it returns false, this
		// is no match.
		if ($this->filter) {
			if (!($this->filter)($op, $storage)) {
				return false;
			}
		}

		// If onMatch callback is specified, call it.
		if ($this->onMatch) {
			($this->onMatch)($op, $storage);
		}

		return true;

	}

}
