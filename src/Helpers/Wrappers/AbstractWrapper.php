<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers\Wrappers;

/**
 * A "wrapper" control structure emulating Python's context manager behavior.
 * This is to provide easy-to-use resource management (so you don't forget
 * anything important after doing something).
 *
 * For details about context managers in Python see:
 * https://docs.python.org/3/library/contextlib.html.
 *
 * Classes may extend this abstract wrapper class and then use it:
 *
 * ```php
 * class YayWrapper extends AbstractWrapper {
 *   public function executeBefore() {
 *     echo "yay enter";
 *   }
 *
 *   public function executeAfter() {
 *     echo "yay exit";
 *   }
 * }
 *
 * $wrapper = new YayWrapper;
 * $wrapper->wrap(function() {
 *   // 'yay enter' will be echoed first.
 *   echo "something.";
 *   // 'yay exit' will be echoed last.
 * });
 * ```
 */
abstract class AbstractWrapper {

	/**
	 * @return mixed
	 */
	public function wrap(callable $fn) {

		$enterRetval = $this->executeBefore();
		try {
			$retval = $fn($enterRetval);
			unset($fn);
			return $retval;
		} finally {
			$this->executeAfter();
		}

	}

	/**
	 * @return mixed
	 */
	abstract public function executeBefore();

	abstract public function executeAfter(): void;

}
