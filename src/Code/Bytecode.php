<?php

declare(strict_types=1);

namespace Smuuf\Primi\Code;

use Smuuf\Primi\VM\Machine;
use Smuuf\StrictObject;

/**
 * Bytecode list object (result from compiling Primi source code).
 *
 * @internal
 */
class Bytecode {

	use StrictObject;

	public readonly int $length;

	/**
	 * @param list<array<mixed>> $ops PHP array of location info of ops.
	 * @param list<OpLocation> $linesInfo
	 */
	public function __construct(
		public readonly array $ops,
		public readonly array $linesInfo = [],
	) {
		$this->length = \count($this->ops);
	}

	public static function buildFake(int $line, int $pos): self {

		return new self(
			[[Machine::OP_RETURN]],
			[new OpLocation($line, $pos)],
		);

	}

	/**
	 * @return list{array<mixed>, list{list{int, int}}}
	 */
	public function __serialize(): array {
		return [
			$this->ops,
			array_map(
				fn(OpLocation $x) => [$x->line, $x->pos],
				$this->linesInfo,
			),
		];
	}

	/**
	 * @param list{array<mixed>, list{list{int, int}}} $data
	 */
	public function __unserialize(array $data): void {

		$this->ops = $data[0];
		$this->linesInfo = array_map(
			fn(array $x) => new OpLocation($x[0], $x[1]),
			$data[1],
		);
		$this->length = \count($this->ops);

	}

}
