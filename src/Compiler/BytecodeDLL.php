<?php

declare(strict_types=1);

namespace Smuuf\Primi\Compiler;

use Smuuf\StrictObject;
use Smuuf\Primi\Compiler\Op;
use Smuuf\Primi\Code\Bytecode;
use Smuuf\Primi\Code\OpLocation;

/**
 * Intermediate bytecode list represented as doubly-linked-list for easier
 * manipulation during compilation.
 *
 * @extends \SplDoublyLinkedList<Op>
 */
class BytecodeDLL extends \SplDoublyLinkedList {

	use StrictObject;

	/**
	 * NOTE: Used primarily in tests.
	 *
	 * @param list<Op> $ops
	 */
	public static function fromArray(array $ops): self {

		$self = new self;
		foreach ($ops as $op) {
			$self->push($op);
		}

		return $self;

	}

	/**
	 * NOTE: Used primarily in tests.
	 *
	 * @return list<Op>
	 */
	public function toArray(): array {
		return iterator_to_array($this);
	}

	public function toFinalBytecode(): Bytecode {

		$ops = [];
		$linesInfo = [];

		// Keep track of "last known line info". Some opcodes synthetized,
		// for example, during postprocess/optimizer pass, may have this
		// information missing, so using this we can fill in the potential gaps.
		$lastKnownLocation = new OpLocation(0, 0);

		/** @var Op $op */
		foreach ($this as $op) {
			$opLoc = $op->opLoc ?? $lastKnownLocation;
			$linesInfo[] = $opLoc;
			$lastKnownLocation = $opLoc;
			$ops[] = [$op->opType, ...$op->args];
		}

		return new Bytecode($ops, $linesInfo);

	}

	/**
	 * Safe way of removing multiple indices (values with these indices) from
	 * a doubly-linked list.
	 *
	 * Doing so naively one-by-one wouldn't work, because doing `unset()`
	 * also decreases index of each of the following items - so we need to
	 * take that into account.
	 *
	 * @param list<int> $indices
	 */
	public function removeIndices(array $indices): void {

		sort($indices);
		$indices = array_unique($indices);
		$offset = 0;

		foreach ($indices as $i) {
			unset($this[$i - $offset]);
			$offset++;
		}

	}

	/**
	 * Replaces some portion (specified by index and length) of DDL with items
	 * from the specified replacement array.
	 *
	 * @param list<Op> $replacement
	 */
	public function splice(
		int $start,
		int $length,
		array $replacement,
	): void {

		if ($start < 0 || ($start + $length) > $this->count()) {
			throw new \OutOfRangeException("Invalid range for splicing");
		}

		for ($i = 0; $i < $length; $i++) {
			$this->offsetUnset($start);
		}

		/** @var Op $item */
		foreach ($replacement as $i => $item) {
			$this->add($start + $i, $item);
		}

	}

	public function seek(int $i): void {

		$this->rewind();
		for ($n = 0; $n < $i; $n++) {
			$this->next();
		}

	}

	public function findLastOpByType(string $opType): ?int {

		$foundIndex = null;

		// Iterate from the top to bottom.
		$iterMode = $this->getIteratorMode();
		$this->setIteratorMode(self::IT_MODE_LIFO);

		try {

			foreach ($this as $i => $op) {
				/** @var Op $op */
				if ($op->opType === $opType) {
					$foundIndex = $i;
					break;
				}
			}

		} finally {

			// Restore the original mode.
			$this->setIteratorMode($iterMode);

		}

		return $foundIndex;

	}

}
