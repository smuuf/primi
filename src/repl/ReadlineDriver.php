<?php

namespace Smuuf\Primi;

class Readline extends \Smuuf\Primi\StrictObject implements IReadlineDriver {

	public function readline(string $prompt): string {
		return readline($prompt);
	}

	public function readlineAddHistory(string $item): void {
		readline_add_history($item);
	}

	public function readlineReadHistory(string $path): void {
		readline_read_history($path);
	}

	public function readlineWriteHistory(string $path): void {
		readline_write_history($path);
	}

}
