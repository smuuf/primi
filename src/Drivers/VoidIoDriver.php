<?php

namespace Smuuf\Primi\Drivers;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Ex\EngineError;

class VoidIoDriver implements StdIoDriverInterface {

	use StrictObject;

	public function input(string $prompt): string {
		throw new EngineError("Trying to get input from void");
	}

	public function stdout(string ...$text): void {
		// Void.
	}

	public function stderr(string ...$text): void {
		// Void.
	}

}
