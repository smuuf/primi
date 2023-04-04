<?php

declare(strict_types=1);

namespace Smuuf\Primi\Compiler;

use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Code\OpLocation;

class Op {

	use \Smuuf\StrictObject;

	/**
	 * @param list<mixed> $args
	 * @phpstan-param Machine::OP_* $opType
	 */
	public function __construct(
		public readonly string $opType,
		public readonly array $args = [],
		public readonly ?OpLocation $opLoc = null,
	) {}

	/**
	 * @param null|list<mixed> $args
	 */
	public function with(
		?string $opType = null,
		?array $args = null,
		?OpLocation $opLoc = null,
	): self {

		return new self(
			opType: $opType ?? $this->opType,
			args: $args ?? $this->args,
			opLoc: $opLoc ?? $this->opLoc,
		);

	}

}
