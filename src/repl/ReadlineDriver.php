<?php

namespace Smuuf\Primi;

class Readline extends \Smuuf\Primi\StrictObject implements IReadlineDriver {

	private $lastItem = '';

	public function readline(string $prompt): string {
		return readline($prompt);
	}

	public function readlineAddHistory(string $item): void {

		// Avoid storing the same value again, if it's the same value
		// as before.
		if ($this->lastItem === $item) {
			return;
		}

		readline_add_history($item);
		$this->lastItem = $item;

	}

	public function readlineReadHistory(string $path): void {

		// Prior to loading the history file:
		// - Read it,
		// - Remove duplicates,
		// - And save it again.
		$entries = file($path, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
		$fixed = array_reverse(array_unique(array_reverse($entries)));
		file_put_contents($path, implode(PHP_EOL, $fixed));

		readline_read_history($path);

	}

	public function readlineWriteHistory(string $path): void {
		readline_write_history($path);
	}

}
