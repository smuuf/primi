<?php

namespace Smuuf\Primi\Drivers;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Cli\Term;

class TerminalIoDriver implements StdIoDriverInterface {

	use StrictObject;

	public function input(string $prompt): string {
		return \readline($prompt);
	}

	public function stdout(string ...$text): void {
		echo \implode('', $text);
	}

	public function stderr(string ...$text): void {
		Term::stderr(\implode('', $text));
	}

}
