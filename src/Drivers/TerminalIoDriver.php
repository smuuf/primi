<?php

declare(strict_types=1);

namespace Smuuf\Primi\Drivers;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Cli\Term;

class TerminalIoDriver implements StdIoDriverInterface {

	use StrictObject;

	private ?string $buffer = null;

	private function bufferInput(string $buffer) {
		$this->buffer = $buffer;
	}

	public function input(string $prompt): string {

		\readline_callback_handler_install($prompt, [$this, 'bufferInput']);

		while (true) {
			\readline_callback_read_char();
			if ($this->buffer !== null) {
				$buffer = $this->buffer;
				$this->buffer = null;
				\readline_callback_handler_remove();
				return $buffer;
			}
		}

	}

	public function stdout(string ...$text): void {
		echo \implode('', $text);
	}

	public function stderr(string ...$text): void {
		Term::stderr(\implode('', $text));
	}

}
