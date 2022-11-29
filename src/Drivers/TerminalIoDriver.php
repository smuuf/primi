<?php

declare(strict_types=1);

namespace Smuuf\Primi\Drivers;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Cli\Term;

class TerminalIoDriver implements StdIoDriverInterface {

	use StrictObject;

	private ?string $buffer = null;

	private function bufferInput(?string $buffer): void {
		$this->buffer = (string) $buffer;
	}

	public function input(string $prompt): string {

		// Using a bit more complex logic instead of just calling readline().
		// That's because with readline() any multiline entries browsed for via
		// the readline history (up arrow) wouldn't be returned in full for
		// some reason (only its last line).

		// Register callback that will be called for every entered line
		// (that is, after the user presses enter).
		\readline_callback_handler_install($prompt, [$this, 'bufferInput']);

		while (\true) {

			// Wait for a single character input.
			\readline_callback_read_char();

			// After the user presses enter, buffer will be (even empty) string,
			// so at that point we know we got the full input we want to return.
			if ($this->buffer !== \null) {
				break;
			}

		}

		\readline_callback_handler_remove();
		$buffer = $this->buffer;
		$this->buffer = \null;

		return $buffer;

	}

	public function stdout(string ...$text): void {
		echo \implode('', $text);
	}

	public function stderr(string ...$text): void {
		Term::stderr(\implode('', $text));
	}

}
