<?php

declare(strict_types=1);

namespace Smuuf\Primi\VM;

use Smuuf\StrictObject;

/**
 * Our wrapper for \SplStack with additional goodies.
 *
 * @internal
 * @extends \SplStack<AbstractValue>
 */
class ValueStack extends \SplStack {

	use StrictObject;

	/**
	 * Helper function for popping N items off the stack and returning it as
	 * a list array.
	 *
	 * List is ordered from from bottom item to top item.
	 *
	 * @return list<AbstractValue>
	 */
	public function popToListRev(int $n): array {

		// Prepare empty PHP array which we're going to fill up from last
		// index to the first. This way we get the bottom->top order of item
		// even though we were popping from the top.
		$list = \array_fill(0, $n, \null);
		for ($i = $n - 1; $i >= 0; $i--) {
			$list[$i] = $this->pop();
		}

		return $list;

	}


	/**
	 * Swaps top of the stack with the N-th item in the stack.
	 */
	public function swap(int $n): void {
		[$this[$n - 1], $this[0]] = [$this[0], $this[$n - 1]];
	}

	/**
	 * Copies N-th from the stack on the top of the stack.
	 */
	public function copy(int $n): void {

		$a = $this[$n - 1];
		$this->push($a);

	}

	public function popN(int $n): void {

		for ($i = 0; $i < $n; $i++) {
			$this->pop();
		}

	}

	public function clear(): void {
		$this->popN(\count($this));
	}

}
